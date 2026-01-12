<?php

namespace App\Http\Controllers;

use App\Models\Espace;
use App\Models\Membre;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Espacetype;
use App\Models\Pays;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use App\Models\Ressourcecompte;
use App\Models\Espaceressource;
use App\Models\Accompagnement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EspaceController extends Controller
{
    public function index()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Afficher uniquement les espaces actifs dans le pays du membre
    $espaces = Espace::with('reservationsAVenir')
        ->with('espacetype')
        ->where('etat', 1)
        ->where('pays_id', $membre->pays_id)
        ->get();

    return view('espace.index', compact('espaces'));
}

    public function show($id)
    {
        $espace = Espace::with('reservationsAVenir')->with('espacetype')->findOrFail($id);
        return view('espace.show', compact('espace'));
    }


public function reserverForm($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $espace = Espace::where('etat', 1)->findOrFail($id);

    // Récupérer les entreprises liées au membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    // Comptes ressources disponibles
    $ressources = Ressourcecompte::where(function ($q) use ($membre, $entrepriseIds) {
            $q->where('membre_id', $membre->id);
            if (!empty($entrepriseIds)) {
                $q->orWhereIn('entreprise_id', $entrepriseIds);
            }
        })
        ->where('etat', 1)
        ->get();

    // Récupérer les accompagnements du membre ou de ses entreprises
    $accompagnements = Accompagnement::where('membre_id', $membre->id)
        ->orWhereIn('entreprise_id', $entrepriseIds)
        ->get();

    return view('espace.reserver', compact('espace', 'ressources', 'accompagnements'));
}


public function reserverStore(Request $request, $id)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    $espace = Espace::where('etat', 1)->findOrFail($id);

    $montant = $request->input('montant') !== null 
        ? (float) $request->input('montant') 
        : (float) ($espace->prix ?? 0);
    $accompagnementId = $request->input('accompagnement_id');

    $rules = [
        'montant' => 'nullable|numeric|min:0',
        'datedebut' => 'required|date|after_or_equal:today',
        'datefin' => 'required|date|after:datedebut',
        'accompagnement_id' => 'required|exists:accompagnements,id',
    ];
    if ($montant > 0) {
        $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
    }
    $request->validate($rules);

    // Vérifier si déjà réservé sur la même période
    $conflict = Reservation::where('espace_id', $espace->id)
        ->where(function ($q) use ($request) {
            $q->whereBetween('datedebut', [$request->datedebut, $request->datefin])
              ->orWhereBetween('datefin', [$request->datedebut, $request->datefin]);
        })
        ->exists();

    if ($conflict) {
        return back()->withInput()->with('error', '⚠️ Cet espace est déjà réservé sur cette période.');
    }

    $ressourcecompte = null;
    if ($montant > 0) {
        $ressourcecompte = Ressourcecompte::where('id', $request->ressourcecompte_id)
            ->where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id)
                    ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->firstOrFail();
    }

    DB::beginTransaction();

    try {
        $reference = 'PAI-ESP-' . strtoupper(Str::random(8));

        if ($montant > 0) {
            // Vérifier compatibilité ressource ↔ offre (espace = offretype_id 4 par ex.)
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 4)
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer un espace.");
            }

            if ($ressourcecompte->solde < $montant) {
                throw new \Exception("⚠️ Solde insuffisant dans ce compte ressource.");
            }

            // Débit du compte payeur
            Ressourcetransaction::create([
                'montant' => -$montant,
                'reference' => $reference,
                'ressourcecompte_id' => $ressourcecompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 2, // débit
                'entreprise_id' => $ressourcecompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $ressourcecompte->decrement('solde', $montant);
        }

        // Enregistrer la trace du paiement (espaceressource)
        Espaceressource::create([
            'montant' => $montant,
            'reference' => $reference,
            'accompagnement_id' => $accompagnementId,
            'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
            'espace_id' => $espace->id,
            'paiementstatut_id' => 1, // 1 = payé
            'membre_id' => $membre->id,
            'entreprise_id' => $ressourcecompte->entreprise_id ?? null,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        // Créer la réservation
        $statutDefaut = Reservationstatut::where('etat', 1)->first();
        Reservation::create([
            'membre_id' => $membre->id,
            'espace_id' => $espace->id,
            'datedebut' => $request->datedebut,
            'datefin' => $request->datefin,
            'observation' => $request->observation,
            'reservationstatut_id' => $statutDefaut?->id,
            'etat' => 1,
            'spotlight' => 0,
        ]);

        DB::commit();

        return redirect()->route('espace.index')
            ->with('success', $montant > 0 ? '✅ Réservation et paiement réussis.' : '✅ Réservation gratuite réussie.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
    }
}



}
