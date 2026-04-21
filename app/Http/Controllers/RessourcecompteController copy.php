<?php

namespace App\Http\Controllers;

use App\Models\Ressourcetype;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Entreprisemembre;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RessourcecompteController extends Controller
{
    /**
     * Liste des ressources du membre connecté et de ses entreprises
     */
    
    public function index()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // IDs des entreprises liées au membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    // Récupérer les types avec les comptes et transactions liées
    $types = Ressourcetype::with(['ressourcecomptes' => function ($q) use ($membre, $entrepriseIds) {
        $q->where('membre_id', $membre->id)
          ->orWhereIn('entreprise_id', $entrepriseIds)
          ->with([
              'ressourcetransactions.operationtype',
              'ressourcetransactions.formationRessource.formation',
              'ressourcetransactions.prestationRessource.prestation',
              'ressourcetransactions.evenementRessource.evenement',
              'ressourcetransactions.espaceRessource.espace', // si tu as ce modèle
          ]);
    }])
    ->orderBy('id')
    ->get();

    return view('ressourcecompte.index', compact('types'));
}


    /**
     * Formulaire d’ajout (uniquement pour le type 1, rattachable au membre ou à ses entreprises)
     */
    public function create()
    {
        $type = Ressourcetype::find(1);
        if (!$type) {
            return redirect()->route('ressourcecompte.index')->with('error', 'Type de ressource non trouvé.');
        }

        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer les entreprises du membre
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->with('entreprise')
            ->get();

        return view('ressourcecompte.create', compact('type', 'entreprises'));
    }

    /**
     * Enregistrement d’une nouvelle ressource pour le membre connecté
     */
    public function store(Request $request)
{
    $request->validate([
        'solde' => 'required|numeric|min:0',
        'entreprise_id' => 'nullable|exists:entreprises,id',
    ]);

    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    // Création du compte ressource
    $ressource = Ressourcecompte::firstOrCreate(
    [
        'membre_id'        => $membre->id,
        'entreprise_id'    => $request->entreprise_id ?? null,
        'ressourcetype_id' => 1, // toujours type 1
    ],
    [
        'solde'     => $request->solde,
        'spotlight' => 0,
        'etat'      => 1,
    ]
);


    $reference = 'TRANS-' . Str::upper(Str::random(8));

    // Création de la transaction initiale
    if ($request->solde > 0) {
        Ressourcetransaction::create([
            'montant'            => $request->solde,
            'reference'          => $reference,
            'ressourcecompte_id' => $ressource->id,
            'datetransaction'    => now(),
            'operationtype_id'   => 1, // à adapter selon ton type "crédit"
            'spotlight'          => 0,
            'etat'               => 1,
        ]);
    }

    return redirect()->route('ressourcecompte.index')->with('success', 'Ressource créée avec succès et transaction initiale enregistrée.');
}

}
