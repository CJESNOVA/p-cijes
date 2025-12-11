<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Ressourcetype;
use App\Models\Operationtype;

class SupabaseSyncService
{
    protected string $baseUrl;
    protected array $headers;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('SUPABASE_URL'), '/') . '/rest/v1';
        $this->headers = [
            'apikey'        => env('SUPABASE_API_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_API_KEY'),
            'Content-Type'  => 'application/json',
        ];
    }

    // ======================================================
    // 🔼 LOCAL → SUPABASE
    // ======================================================

    /**
     * 🔹 Synchronise un compte local vers Supabase
     */
    public function pushAccount(Ressourcecompte $compte): void
    {
        $compte->loadMissing(['ressourcetype', 'entreprise', 'membre.user']);

        $payload = [
            'balance'       => $compte->solde ?? 0,
            'resource_type' => $compte->ressourcetype?->titre ?? 'Inconnu',
            'startup_id'    => $compte->entreprise?->supabase_startup_id,
            'user_id'       => $compte->membre?->user?->supabase_user_id,
            'updated_at'    => now()->toIso8601String(),
        ];

        // Vérification cohérence minimale
        if (empty($payload['user_id']) && empty($payload['startup_id'])) {
            Log::warning("⏭️ Compte ignoré : aucun user_id ni startup_id.", [
                'compte_id' => $compte->id,
                'payload'   => $payload,
            ]);
            return;
        }

        try {
            if (!empty($compte->resource_account_id)) {
                // 🔁 UPDATE sur Supabase
                $response = Http::withHeaders($this->headers)
                    ->patch("{$this->baseUrl}/resource_accounts?id=eq.{$compte->resource_account_id}", $payload);

                if ($response->failed()) {
                    Log::error("❌ Échec de la mise à jour du compte Supabase", [
                        'compte_id' => $compte->id,
                        'error'     => $response->body(),
                        'status'    => $response->status(),
                    ]);
                } else {
                    Log::info("🔁 Compte Supabase mis à jour avec succès", [
                        'compte_id'  => $compte->id,
                        'supabase_id'=> $compte->resource_account_id,
                    ]);
                }
            } else {
                // 🆕 CREATE sur Supabase
                $response = Http::withHeaders(array_merge($this->headers, [
                                    'Prefer' => 'return=representation'
                                ]))
                    ->post("{$this->baseUrl}/resource_accounts", $payload);

                if ($response->failed()) {
                    Log::error("❌ Échec de la création du compte Supabase", [
                        'compte_id' => $compte->id,
                        'error'     => $response->body(),
                        'status'    => $response->status(),
                    ]);
                } else {
                    $responseData = $response->json();
                    $remoteId = $responseData['id'] ?? ($responseData[0]['id'] ?? null);

                    if ($remoteId) {
                        $compte->update(['resource_account_id' => $remoteId]);
                    }

                    Log::info("✅ Compte Supabase créé avec succès", [
                        'compte_id' => $compte->id,
                        'response'  => $responseData,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("💥 Exception lors du push du compte Supabase", [
                'compte_id' => $compte->id,
                'message'   => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * 🔹 Synchronise une transaction locale vers Supabase
     */
    public function pushTransaction(Ressourcetransaction $transaction): void
    {
        $transaction->loadMissing(['ressourcecompte', 'operationtype']);

        $payload = [
            'amount'              => $transaction->montant ?? 0,
            'reference'           => $transaction->reference,
            'resource_account_id' => $transaction->ressourcecompte?->resource_account_id,
            'operation_type'      => $transaction->operationtype?->titre ?? '+',
            'status'              => $transaction->etat ?? 0,
            'method'              => 'Tmoney',
            'notes'               => 'SEMOA',
            'updated_at'          => now()->toIso8601String(),
        ];

        if (empty($payload['resource_account_id'])) {
            Log::warning("⏭️ Transaction ignorée : compte introuvable.", [
                'transaction_id' => $transaction->id,
            ]);
            return;
        }

        try {
            $response = Http::withHeaders(array_merge($this->headers, [
                    'Prefer' => 'return=representation'
                ]))
                ->post("{$this->baseUrl}/resource_transactions", $payload);

            if ($response->failed()) {
                Log::error("❌ Échec du push transaction Supabase", [
                    'transaction_id' => $transaction->id,
                    'error'          => $response->body(),
                    'status'         => $response->status(),
                ]);
            } else {
                $responseData = $response->json();
                $remoteId = $responseData['id'] ?? ($responseData[0]['id'] ?? null);

                if ($remoteId) {
                    $transaction->update(['resource_transaction_id' => $remoteId]);
                }

                Log::info("✅ Transaction synchronisée avec succès.", [
                    'transaction_id' => $transaction->id,
                    'response'       => $responseData,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("💥 Exception lors du push de la transaction Supabase", [
                'transaction_id' => $transaction->id,
                'message'        => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * 🔁 Synchronise tous les comptes et transactions locales vers Supabase.
     * Aucun élément n’est rapatrié depuis Supabase.
     */
    public function syncAllToSupabase(): void
    {
        Log::info("🚀 DÉBUT SYNCHRONISATION → SUPABASE");

        try {
            // 1️⃣ Récupération de tous les comptes actifs avec leurs relations utiles
            $comptes = Ressourcecompte::with([
                'ressourcetype',
                'entreprise',
                'membre.user',
                'ressourcetransactions.operationtype'
            ])
            ->where('etat', 1)
            ->get();

            $totalComptes = $comptes->count();
            $totalTransactions = 0;
            $compteSuccess = 0;
            $compteErrors = 0;

            Log::info("📦 {$totalComptes} comptes détectés pour synchronisation.");

            foreach ($comptes as $compte) {
                try {
                    // 2️⃣ Synchronisation du compte
                    $this->pushAccount($compte);
                    $compteSuccess++;

                    Log::info("✅ Compte synchronisé (#{$compte->id})", [
                        'membre'       => $compte->membre?->id,
                        'entreprise'   => $compte->entreprise?->id,
                        'ressourcetype'=> $compte->ressourcetype?->titre,
                    ]);

                    // 3️⃣ Synchronisation des transactions liées
                    $transactions = $compte->ressourcetransactions ?? collect();
                    $totalTransactions += $transactions->count();

                    foreach ($transactions as $transaction) {
                        try {
                            $this->pushTransaction($transaction);
                        } catch (\Exception $te) {
                            Log::error("❌ Erreur transaction #{$transaction->id}", [
                                'message' => $te->getMessage(),
                                'trace'   => $te->getTraceAsString(),
                            ]);
                        }
                    }

                    Log::info("💰 {$transactions->count()} transactions envoyées pour le compte #{$compte->id}");

                } catch (\Exception $ce) {
                    $compteErrors++;
                    Log::error("💥 Erreur lors de la synchronisation du compte #{$compte->id}", [
                        'message' => $ce->getMessage(),
                        'trace'   => $ce->getTraceAsString(),
                    ]);
                }
            }

            Log::info("🎯 SYNCHRONISATION TERMINÉE", [
                'comptes_total'         => $totalComptes,
                'comptes_synchronisés'  => $compteSuccess,
                'comptes_erreurs'       => $compteErrors,
                'transactions_envoyées' => $totalTransactions,
                'timestamp'             => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            Log::critical("💣 ÉCHEC GLOBAL DE LA SYNCHRONISATION SUPABASE", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

        Log::info("🏁 FIN SYNCHRONISATION → SUPABASE");
    }
}
