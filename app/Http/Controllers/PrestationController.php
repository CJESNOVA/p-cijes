<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\Prestationtype;
use App\Models\Pays;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Prestationrealisee;
use App\Models\Prestationrealiseestatut;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use App\Models\Ressourcecompte;
use App\Models\Prestationressource;
use App\Models\Accompagnement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrestationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupère toutes les entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Récupère les prestations liées uniquement à ces entreprises
        $prestations = Prestation::whereIn('entreprise_id', $entrepriseIds)
            ->with(['prestationtype', 'entreprise'])
            ->orderByDesc('id')
            ->get();

        return view('prestation.index', compact('prestations'));
    }


    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->get();

        $prestationtypes = Prestationtype::where('etat', 1)->get();

        return view('prestation.form', [
            'prestation' => null,
            'prestationtypes' => $prestationtypes,
            'pays_id' => $membre->pays_id,
            'entreprises' => $entreprises,
        ]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->get();

        $prestation = Prestation::/*where('entreprise_id', $entreprise->id)->*/findOrFail($id);
        $prestationtypes = Prestationtype::where('etat', 1)->get();

        return view('prestation.form', compact('prestation', 'prestationtypes', 'entreprises'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();

        $validated = $this->validateData($request);
        //$validated['entreprise_id'] = $entreprises->id;
        $validated['pays_id'] = $membre->pays_id;
        $validated['etat'] = 1;
        $validated['spotlight'] = $request->has('spotlight') ? 1 : 0;

        Prestation::create($validated);

        return redirect()->route('prestation.index')->with('success', 'Prestation créée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();

        $prestation = Prestation::findOrFail($id);//where('entreprise_id', $entreprise->id)->

        $validated = $this->validateData($request);
        //$validated['spotlight'] = $request->has('spotlight') ? 1 : 0;

        $prestation->update($validated);

        return redirect()->route('prestation.index')->with('success', 'Prestation mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();

        $prestation = Prestation::findOrFail($id);//where('entreprise_id', $entreprise->id)->
        $prestation->delete();

        return redirect()->route('prestation.index')->with('success', 'Prestation supprimée avec succès.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'prix' => 'required|numeric|min:0',
            'duree' => 'required|string|max:100',
            'prestationtype_id' => 'required|exists:prestationtypes,id',
            'titre' => 'nullable|string',
            'description' => 'nullable|string',
            'entreprise_id' => 'nullable|string',
        ]);
    }

    // Liste des prestations ouvertes
    public function liste()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer toutes les entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Récupérer les prestations du pays du membre ou de ses entreprises
        $prestations = Prestation::with(['prestationtype', 'entreprise'])
            ->where('etat', 1)
            ->where(function($q) use ($membre, $entrepriseIds) {
                $q->where('pays_id', $membre->pays_id)
                ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        $membreId = $membre->id;

        return view('prestation.liste', compact('prestations', 'membreId'));
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

    // Récupérer la prestation
    $prestation = Prestation::where('etat', 1)->findOrFail($id);

    // IDs des entreprises liées au membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    // Comptes ressources disponibles
    $ressources = Ressourcecompte::where(function($q) use ($membre, $entrepriseIds) {
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

    return view('prestation.inscrire', compact('prestation', 'ressources', 'accompagnements'));
}


public function inscrireStore(Request $request, $id)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();

    $prestation = Prestation::where('etat', 1)->findOrFail($id);

    // IDs des entreprises liées au membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    $montant = (float) $request->input('montant', 0);
    $accompagnementId = $request->input('accompagnement_id');

    // Validation
    $rules = [
        'montant' => 'nullable|numeric|min:0',
        'accompagnement_id' => 'required|exists:accompagnements,id',
    ];

    if ($montant > 0) {
        $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
    } else {
        $rules['ressourcecompte_id'] = 'nullable|exists:ressourcecomptes,id';
    }

    $request->validate($rules);

    // Vérifier si déjà réalisée via cet accompagnement
    if (Prestationrealisee::where('prestation_id', $prestation->id)
        ->where('accompagnement_id', $accompagnementId)
        ->exists()
    ) {
        return back()->with('error', '⚠️ Cette prestation a déjà été enregistrée pour cet accompagnement.');
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

    // Receveur : entreprise propriétaire de la prestation
    $receveurEntrepriseId = $prestation->entreprise_id;
    $receveurCompte = Ressourcecompte::firstOrCreate(
        ['entreprise_id' => $receveurEntrepriseId, 'ressourcetype_id' => 1],
        ['membre_id' => null, 'solde' => 0, 'etat' => 1, 'spotlight' => 0]
    );

    DB::beginTransaction();

    try {
        $reference = 'PAI-PREST-' . strtoupper(Str::random(8));

        if ($montant > 0) {
            // Vérifier compatibilité ressource ↔ prestation
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 1) // 1 = prestation
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer une prestation.");
            }

            if ($ressourcecompte->solde < $montant) {
                throw new \Exception("⚠️ Solde insuffisant dans ce compte ressource.");
            }

            // Débit
            Ressourcetransaction::create([
                'montant' => -$montant,
                'reference' => $reference,
                'ressourcecompte_id' => $ressourcecompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 2,
                'entreprise_id' => $ressourcecompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $ressourcecompte->decrement('solde', $montant);

            // Crédit
            Ressourcetransaction::create([
                'montant' => $montant,
                'reference' => $reference,
                'ressourcecompte_id' => $receveurCompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 1,
                'entreprise_id' => $receveurCompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $receveurCompte->increment('solde', $montant);
        }


            // Enregistrer la trace du paiement (prestationressource)
            Prestationressource::create([
                'montant' => $montant,
                'reference' => $reference,
                'accompagnement_id' => $accompagnementId,
                'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
                'prestation_id' => $prestation->id,
                'paiementstatut_id' => 1, // adapter selon ton mapping (1 = payé)
                'membre_id' => $membre->id,
                'entreprise_id' => $ressourcecompte->entreprise_id ?? null,
                'spotlight' => 0,
                'etat' => 1,
            ]);

        // Créer la prestation réalisée
        $statutDefaut = Prestationrealiseestatut::where('etat', 1)->first();
        Prestationrealisee::create([
            'prestation_id' => $prestation->id,
            'accompagnement_id' => $accompagnementId,
            'daterealisation' => now(),
            'prestationrealiseestatut_id' => $statutDefaut?->id,
            'note' => null,
            'feedback' => null,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        DB::commit();

        return redirect()->route('prestation.liste')
            ->with('success', $montant > 0 ? '✅ Prestation enregistrée et payée.' : '✅ Prestation enregistrée gratuitement.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', '⚠️ Erreur : ' . $e->getMessage());
    }
}


}
