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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * Formulaire d’ajout
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

        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->with('entreprise')
            ->get();


        /*Ressourcecompte::create([
            'user_id' => 1,
            'solde' => 1000,
            'etat' => 1,
        ]);

        Ressourcetransaction::create([
            'ressourcecompte_id' => 1,
            'montant' => 200,
            //'type' => 'credit',
            'etat' => 1,
        ]);*/


        return view('ressourcecompte.create', compact('type', 'entreprises'));
    }



    /**
     * Paiement SEMOA (au lieu d’ajouter directement un solde)
     */
    public function store(Request $request)
    {
        $request->validate([
            'solde' => 'required|numeric|min:100',
            'entreprise_id' => 'nullable|exists:entreprises,id',
        ]);

        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()->route('membre.createOrEdit')->with('error', 'Veuillez créer votre profil membre.');
        }
        
        // 🔹 1. Rechercher un compte ressource existant
        $ressource = Ressourcecompte::where('membre_id', $membre->id)
            ->where('entreprise_id', $request->entreprise_id ?? null)
            ->where('ressourcetype_id', 1)
            ->first();

        // 🔹 2. S’il n’existe pas, on le crée
        if (!$ressource) {
            $ressource = Ressourcecompte::create([
                'membre_id'        => $membre->id,
                'entreprise_id'    => $request->entreprise_id ?? null,
                'ressourcetype_id' => 1,
                'solde'            => 0,
                'spotlight'        => 0,
                'etat'             => 1,
            ]);

            // 💬 Optionnel : journaliser la création
            // Log::info("✅ Nouveau compte ressource créé pour membre #{$membre->id}");
        } 
        // 🔹 3. S’il existe déjà, on peut le mettre à jour (si besoin)
        else {
            $ressource->update([
                'etat'      => 1, // exemple de champ à mettre à jour
                'updated_at'=> now(),
            ]);

            // 💬 Optionnel : journaliser la mise à jour
            //Log::info("🔁 Compte ressource mis à jour pour membre #{$membre->id}");
        }

        // 🔹 2. Créer une transaction en attente
        $reference = 'TRANS-' . Str::upper(Str::random(8));
        $transaction = Ressourcetransaction::create([
            'montant'            => $request->solde,
            'reference'          => $reference,
            'ressourcecompte_id' => $ressource->id,
            'datetransaction'    => now(),
            'operationtype_id'   => 1, // crédit
            'spotlight'          => 0,
            'etat'               => 0, // en attente
        ]);

/*
    $login  = 'api_cashpay.amsar';
    $apikey = 'poSoWdqsv2iyQ3wKHA1hMHW6myE4bA4iaN0j';
    $salt   = rand(100000, 999999); // identifiant unique
    $apiref = '252';

    // 1️⃣ Vérification d’authentification (Ping)
    $apisecure = hash('sha256', $login . $apikey . $salt);

    // 2️⃣ Création de l’ordre
    $response = Http::withHeaders([
    'login'        => $login,
    'apisecure'    => $apisecure,
    'apireference' => $apiref,
    'salt'         => $salt,
    'Content-Type' => 'application/json',
])->post('https://api.semoa-payments.ovh/prod/orders', [
    'amount'       => (int) $request->solde, // <- ici le montant réel
    'description'  => 'Recharge de ressource de ' . $transaction->reference . '',
    'client'       => [
        'lastname'  => $membre->nom ?? 'Inconnu',
        'firstname' => $membre->prenom ?? '',
        'phone'     => '+228' . ($membre->telephone ?? '90000000'),
    ],
    'callback_url' => route('ressourcecompte.callback', [$transaction->id]),
    'redirect_url' => route('ressourcecompte.index'),
]);

//dd($response->json());

return redirect()->away($response->json('bill_url'));

//dd($response);

    if ($response->failed()) {
        return response()->json(['error' => 'Erreur création paiement', 'details' => $response->json()], 400);
    }

    return response()->json($response->json());*/
            return redirect()->route('ressourcecompte.index')->with('success', 'Bien.');

    }



    /**
     * Callback SEMOA
     */
    /*public function callback($transactionId, $amount, Request $request)
    {
        $transaction = Ressourcetransaction::find($transactionId);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction inconnue'], 404);
        }

        $status = strtoupper($request->get('status', 'FAILED'));

        if ($status === 'SUCCESS') {
            $transaction->update(['etat' => 1]); // validé

            // Créditer le compte ressource
            $compte = $transaction->ressourcecompte;
            $compte->increment('solde', $transaction->montant);

            return response()->json(['message' => 'Paiement confirmé et solde crédité'], 200);
        } else {
            $transaction->update(['etat' => -1]); // échec ou annulé
            return response()->json(['message' => 'Paiement non validé'], 400);
        }
    }*/
        
        public function callback(Request $request, $transactionId)
{
    // On cherche la transaction
    $transaction = Ressourcetransaction::find($transactionId);
    if (!$transaction) {
        return response()->json(['error' => 'Transaction introuvable'], 404);
    }

    // On récupère le status depuis le body JSON
    $status = strtoupper($request->input('status', 'FAILED')); 

    if ($status === 'SUCCESS') {
        // Transaction validée
        $transaction->update(['etat' => 1]); 

        // Créditer le compte ressource
        $compte = $transaction->ressourcecompte;
        if ($compte) {
            $compte->increment('solde', $transaction->montant);
        }

        return response()->json([
            'message' => 'Paiement confirmé et solde crédité',
            'transaction_id' => $transaction->id,
            'new_solde' => $compte->solde ?? null
        ]);
    }

    // Transaction échouée
    $transaction->update(['etat' => -1]);
    return response()->json(['message' => 'Paiement non validé']);
}


}
