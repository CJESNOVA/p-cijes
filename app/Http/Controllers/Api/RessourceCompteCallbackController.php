<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcecompte;

class RessourceCompteCallbackController extends Controller
{
    /**
     * Callback SEMOA → appel externe SANS CSRF
     * URL : POST /api/callback/ressourcecompte/{transaction}
     */
    public function handle($transactionId, Request $request)
    {
        $payload = $request->all();

        Log::info("📩 Callback SEMOA reçu", [
            'transaction_id' => $transactionId,
            'payload' => $payload,
            'method' => $request->method(),
            'headers' => $request->headers->all(),
        ]);

        // Récupérer la transaction locale
        $transaction = Ressourcetransaction::find($transactionId);

        if (!$transaction) {
            Log::warning("⚠️ Transaction introuvable", ['id' => $transactionId]);

            return response()->json([
                'error' => 'Transaction inconnue',
            ], 404);
        }

        Log::info("📋 Transaction trouvée", [
            'id' => $transaction->id,
            'etat_actuel' => $transaction->etat,
            'montant' => $transaction->montant,
            'ressourcecompte_id' => $transaction->ressourcecompte_id,
        ]);

        // Décision succès / échec
        $success = false;

        if (
            (isset($payload['state']) && strtolower($payload['state']) == 'paid') ||
            (isset($payload['state']) && strtolower($payload['state']) == 'success') ||
            (isset($payload['received_amount']) && (int)$payload['received_amount'] >= (int)$transaction->montant)
        ) {
            $success = true;
        }

        // Mise à jour transaction
        $ancienEtat = $transaction->etat;
        $transaction->etat = $success ? 1 : -1;
        $transaction->save();

        Log::info("🔄 Transaction mise à jour", [
            'transaction_id' => $transaction->id,
            'ancien_etat' => $ancienEtat,
            'nouvel_etat' => $transaction->etat,
            'success' => $success,
        ]);

        // Recalcul total du solde
        //$compte = $transaction->ressourcecompte;
        $compte = \App\Models\Ressourcecompte::find($transaction->ressourcecompte_id);

        if ($compte) {
            Log::info("💼 Compte trouvé", [
                'compte_id' => $compte->id,
                'solde_actuel' => $compte->solde,
            ]);
            $nouveauSolde = Ressourcetransaction::where('ressourcecompte_id', $compte->id)
                ->where('etat', 1) // uniquement validées
                ->get()
                ->reduce(function ($total, $t) {
                    switch ($t->operationtype_id) {
                        case 1: // Crédit
                            return $total + $t->montant;
                        case 2: // Débit
                            return $total - $t->montant;
                        case 3: // Conversion
                            return $total + $t->montant; // selon ton modèle
                        case 4: // Retrait
                            return $total - $t->montant;
                        case 5: // Remboursement
                            return $total + $t->montant;
                    }
                    return $total;
                }, 0);

            // Mise à jour propre du solde
            $compte->solde = $nouveauSolde;
            $compte->save();

            Log::info("💰 Solde recalculé avec succès", [
                'compte_id' => $compte->id,
                'solde' => $nouveauSolde,
            ]);
        }

        Log::info("✅ Callback traité", [
            'transaction_id' => $transaction->id,
            'success' => $success,
        ]);

        return response()->json([
            'message' => 'Callback reçu et traité',
            'success' => $success,
            'transaction_id' => $transaction->id,
        ], 200);
    }

}
