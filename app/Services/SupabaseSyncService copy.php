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
     * Synchronise un compte local vers Supabase
     */
    public function pushAccount(Ressourcecompte $compte): void
{
    $compte->loadMissing(['ressourcetype', 'entreprise', 'membre.user']);

    $payload = [
        'balance'        => $compte->solde ?? 0,
        'resource_type'  => $compte->ressourcetype?->titre ?? 'Inconnu',
        'startup_id'     => $compte->entreprise?->supabase_startup_id,
        'user_id'        => $compte->membre?->user?->supabase_user_id,
        'updated_at'     => now()->toIso8601String(),
    ];

    if (empty($payload['user_id']) && empty($payload['startup_id'])) {
        Log::warning("⏭️ Compte ignoré : aucun user_id ni startup_id.", [
            'compte_id' => $compte->id,
            'payload'   => $payload,
        ]);
        return;
    }

    try {
        if (!empty($compte->resource_account_id)) {
            // ===================================================
            // 🔁 UPDATE sur Supabase
            // ===================================================
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
                    'compte_id'   => $compte->id,
                    'supabase_id' => $compte->resource_account_id,
                ]);
            }

        } else {
            // ===================================================
            // 🆕 CREATE sur Supabase
            // ===================================================
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
                $data = $response->json();
                $remoteId = $data[0]['id'] ?? $data['id'] ?? null; // Supabase renvoie souvent un tableau JSON

                if ($remoteId) {
                    \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($compte, $remoteId) {
                        $compte->update([
                            'resource_account_id' => $remoteId,
                            'updated_at'          => now(),
                        ]);
                    });
                }

                Log::info("✅ Compte Supabase créé avec succès", [
                    'compte_id' => $compte->id,
                    'response'  => $data,
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
     * Synchronise une transaction locale vers Supabase
     */
    public function pushTransaction(Ressourcetransaction $transaction): void
{
    $transaction->loadMissing(['ressourcecompte', 'operationtype']);

    $payload = [
        'amount'               => $transaction->montant ?? 0,
        'reference'            => $transaction->reference ?? null,
        'resource_account_id'  => $transaction->ressourcecompte?->resource_account_id,
        'operation_type'       => $transaction->operationtype?->titre ?? 'Non défini',
        'status'               => $transaction->etat ?? 0,
        'method'               => 'Tmoney',
        'notes'                => 'SEMOA',
        'updated_at'           => now()->toIso8601String(),
    ];

    // 🧩 Vérif cohérence minimale
    if (empty($payload['resource_account_id'])) {
        Log::warning("⏭️ Transaction ignorée : compte introuvable.", [
            'transaction_id' => $transaction->id,
            'payload' => $payload,
        ]);
        return;
    }

    try {
        if (!empty($transaction->resource_transaction_id)) {
            // ===================================================
            // 🔁 UPDATE sur Supabase (si l’ID distant existe)
            // ===================================================
            $response = Http::withHeaders($this->headers)
                ->patch("{$this->baseUrl}/resource_transactions?id=eq.{$transaction->resource_transaction_id}", $payload);

            if ($response->failed()) {
                Log::error("❌ Échec de la mise à jour transaction Supabase", [
                    'transaction_id' => $transaction->id,
                    'error' => $response->body(),
                    'status' => $response->status(),
                ]);
            } else {
                Log::info("🔁 Transaction Supabase mise à jour avec succès", [
                    'transaction_id' => $transaction->id,
                    'remote_id' => $transaction->resource_transaction_id,
                ]);
            }

        } else {
            // ===================================================
            // 🆕 CREATE sur Supabase (si aucun ID distant)
            // ===================================================
            $response = Http::withHeaders(array_merge($this->headers, [
                    'Prefer' => 'return=representation'
                ]))
                ->post("{$this->baseUrl}/resource_transactions", $payload);

            if ($response->failed()) {
                Log::error("❌ Échec de la création transaction Supabase", [
                    'transaction_id' => $transaction->id,
                    'error' => $response->body(),
                    'status' => $response->status(),
                ]);
            } else {
                $data = $response->json();
                $remoteId = $data[0]['id'] ?? $data['id'] ?? null;

                if ($remoteId) {
                    \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($transaction, $remoteId) {
                        $transaction->update([
                            'resource_transaction_id' => $remoteId,
                            'updated_at'              => now(),
                        ]);
                    });
                }

                Log::info("✅ Transaction Supabase créée avec succès", [
                    'transaction_id' => $transaction->id,
                    'response'       => $data,
                ]);
            }
        }

    } catch (\Exception $e) {
        Log::error("💥 Exception lors du push transaction Supabase", [
            'transaction_id' => $transaction->id,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}

    
}
