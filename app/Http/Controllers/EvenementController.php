<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Membre;
use App\Models\Evenementinscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evenementtype;
use App\Models\Pays;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use App\Models\Ressourcecompte;
use App\Models\Evenementressource;
use App\Models\Accompagnement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EvenementController extends Controller
{
    // Liste des événements disponibles
    public function index()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $evenements = Evenement::with(['evenementtype'])
        ->where('etat', 1)
        ->where('pays_id', $membre->pays_id)
        ->whereDate('dateevenement', '>=', now())
        ->orderBy('dateevenement', 'asc')
        ->get();

    return view('evenement.index', compact('evenements'));
}



    // Détails d’un événement
    public function show(Evenement $evenement)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();

        $dejaInscrit = null;

        if ($membre) {
            $dejaInscrit = $evenement->inscriptions()
                ->where('membre_id', $membre->id)
                ->with('evenementinscriptiontype') // charger la relation pour Blade
                ->first(); // récupérer l'objet inscription ou null
        }

        return view('evenement.show', compact('evenement', 'dejaInscrit'));
    }



    public function inscrireForm($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $evenement = Evenement::where('etat', 1)->findOrFail($id);

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

    return view('evenement.inscrire', compact('evenement', 'ressources', 'accompagnements'));
}


public function inscrireStore(Request $request, $id)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    $evenement = Evenement::where('etat', 1)->findOrFail($id);

    $montant = $request->input('montant') !== null 
        ? (float) $request->input('montant') 
        : (float) ($evenement->prix ?? 0);
    $accompagnementId = $request->input('accompagnement_id');

    $rules = [
        'montant' => 'nullable|numeric|min:0',
        'accompagnement_id' => 'required|exists:accompagnements,id',
    ];
    if ($montant > 0) {
        $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
    }
    $request->validate($rules);

    // Vérifier si déjà inscrit
    if (Evenementinscription::where('membre_id', $membre->id)->where('evenement_id', $evenement->id)->exists()) {
        return back()->withInput()->with('error', '⚠️ Vous êtes déjà inscrit à cet événement.');
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
        $reference = 'PAI-EVT-' . strtoupper(Str::random(8));

        if ($montant > 0) {
            // Vérifier compatibilité ressource ↔ offre (événement = offretype_id 3)
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 3)
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer un événement.");
            }

            if ($ressourcecompte->solde < $montant) {
                throw new \Exception("⚠️ Solde insuffisant dans ce compte ressource.");
            }

            // Débit du compte payeur (pas de crédit receveur car événement neutre)
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


            // Enregistrer la trace du paiement (evenementressource)
            Evenementressource::create([
                'montant' => $montant,
                'reference' => $reference,
                'accompagnement_id' => $accompagnementId,
                'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
                'evenement_id' => $evenement->id,
                'paiementstatut_id' => 1, // adapter selon ton mapping (1 = payé)
                'membre_id' => $membre->id,
                'entreprise_id' => $ressourcecompte->entreprise_id ?? null,
                'spotlight' => 0,
                'etat' => 1,
            ]);


        // Créer l’inscription
        $statutDefaut = Evenementinscriptiontype::where('etat', 1)->first();
        Evenementinscription::create([
            'membre_id' => $membre->id,
            'evenement_id' => $evenement->id,
            'dateevenementinscription' => now(),
            'evenementinscriptiontype_id' => $statutDefaut?->id,
            'etat' => 1,
            'spotlight' => 0,
        ]);

        DB::commit();

        return redirect()->route('evenement.index')
            ->with('success', $montant > 0 ? '✅ Inscription et paiement réussis.' : '✅ Inscription gratuite réussie.');

    } catch (\Exception $e) {
        DB::rollBack();
        /*Log::error('Inscription événement error: ' . $e->getMessage(), [
            'user_id' => $membre->id,
            'evenement_id' => $evenement->id,
        ]);*/
        return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
    }
}

}
