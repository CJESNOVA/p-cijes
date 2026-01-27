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

    // Récupérer les types avec les comptes et transactions (par ordre décroissant)
    $types = Ressourcetype::with(['ressourcecomptes' => function ($q) use ($membre, $entrepriseIds) {
        $q->where('membre_id', $membre->id)
          ->orWhereIn('entreprise_id', $entrepriseIds)
          ->with(['ressourcetransactions' => function ($t) {
              $t->with([
                  'operationtype',
                  'formationRessource.formation',
                  'prestationRessource.prestation',
                  'evenementRessource.evenement',
                  'espaceRessource.espace',
                  'cotisationRessource.cotisation',
              ])
              ->where('etat', 1)
              ->orderByDesc('created_at'); // ✅ transactions les plus récentes en premier
          }]);
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

        return view('ressourcecompte.create', compact('type', 'entreprises'));
    }



    /**
     * Paiement SEMOA (au lieu d’ajouter directement un solde)
     */
public function store(Request $request)
{
    // ----------------------------
    // 🔹 1. Validation
    // ----------------------------
    $request->validate([
        'solde' => 'required|numeric|min:100',
        'entreprise_id' => 'nullable|exists:entreprises,id',
    ]);

    // ----------------------------
    // 🔹 2. Récupérer le membre connecté
    // ----------------------------
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', 'Veuillez créer votre profil membre.');
    }

    // ----------------------------
    // 🔹 3. Récupérer / créer compte ressource
    // ----------------------------
    $ressource = Ressourcecompte::firstOrCreate(
        [
            'membre_id'        => $membre->id,
            'entreprise_id'    => $request->entreprise_id,
            'ressourcetype_id' => 1,
        ],
        [
            'solde'     => 0,
            'spotlight' => 0,
            'etat'      => 1,
        ]
    );

    // ----------------------------
    // 🔹 4. Créer la transaction locale
    // ----------------------------
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

    // ----------------------------
    // 🔹 5. Préparer l'appel API SEMOA
    // ----------------------------
    $login  = env('SEMOA_API_LOGIN', 'api_cashpay.cjet');
    $apikey = env('SEMOA_API_KEY', 'k3cGVCzeetDmU4wuRx0gUyHilrhJB08dKprD');
    $salt   = rand(100000, 999999); // identifiant unique
    $apiref = env('SEMOA_API_REFERENCE', '336');

    // 1️⃣ Vérification d'authentification (Ping)
    $apisecure = hash('sha256', $login . $apikey . $salt);

    // 2️⃣ Création de l'ordre
    $response = Http::withHeaders([
    'login'        => $login,
    'apisecure'    => $apisecure,
    'apireference' => $apiref,
    'salt'         => $salt,
    'Content-Type' => 'application/json',
])->post(env('SEMOA_API_URL', 'https://api.semoa-payments.ovh/prod/orders'), [
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

    // ----------------------------
    // 🔹 7. Traitement retour API
    // ----------------------------
    if ($response->failed()) {
        Log::error("❌ Erreur SEMOA", [
            'transaction' => $transaction->id,
            'response'    => $response->json(),
            'http_code'   => $response->status(),
        ]);

        return back()->with('error', 'Erreur création paiement SEMOA : ' . json_encode($response->json()));
    }

    $billUrl = $response->json('bill_url');

    if (!$billUrl) {
        Log::error("❌ SEMOA : bill_url manquant", [
            'transaction' => $transaction->id,
            'response'    => $response->json(),
        ]);

        return back()->with('error', 'Erreur : lien de paiement introuvable.');
    }

    Log::info("💳 Paiement SEMOA créé", [
        'transaction' => $transaction->id,
        'bill_url'    => $billUrl,
    ]);
//dd($response->json());
    // ----------------------------
    // 🔹 8. Redirection vers la page de paiement
    // ----------------------------
    return redirect()->away($billUrl);
}






public function callback($id, Request $request)
{
    // SEMOA peut envoyer JSON ou form-data
    $payload = $request->all();

    Log::info("📩 Callback SEMOA reçu", [
        'transaction_id' => $id,
        'payload' => $payload,
    ]);

    // Récupérer transaction locale
    $transaction = \App\Models\Ressourcetransaction::find($id);

    if (!$transaction) {
        Log::warning("⚠️ Transaction introuvable", ['id' => $id]);
        return response()->json(['error' => 'Transaction inconnue'], 404);
    }

    // Vérif du statut SEMOA
    $status = $payload['state'] ?? $payload['status'] ?? null;

    if (!$status) {
        return response()->json(['error' => 'State manquant'], 400);
    }

    // Traitement
    if (strtolower($status) === 'success' || strtolower($status) === 'paid') {

        // Marquer transaction comme validée
        $transaction->update(['etat' => 1]);

        // Créditer le compte
        $compte = $transaction->ressourcecompte;
        $compte->increment('solde', $transaction->montant);

        Log::info("💰 Paiement validé et solde crédité", [
            'transaction_id' => $transaction->id,
            'nouveau_solde' => $compte->solde
        ]);

        return response()->json(['status' => 'ok'], 200);
    }

    // Traitement si échec
    $transaction->update(['etat' => -1]);

    Log::warning("❌ Paiement SEMOA échoué", [
        'transaction_id' => $id,
        'state' => $status
    ]);

    return response()->json(['status' => 'failed'], 200);
}



private function recalculateCompteSolde(Ressourcecompte $compte)
{
    $transactions = $compte->ressourcetransactions()
        ->where('etat', 1)
        ->with('operationtype_id')
        ->get();

    $solde = 0;

    foreach ($transactions as $t) {

        switch ($t->operationtype_id) {
            case 1: // Crédit
            case 5: // Remboursement
                $solde += $t->montant;
                break;

            case 2: // Débit
            case 4: // Retrait
                $solde -= $t->montant;
                break;

            case 3: // Conversion
                $solde += $t->montant;
                break;
        }
    }

    $compte->update(['solde' => $solde]);
}



}
