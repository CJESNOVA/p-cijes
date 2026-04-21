<?php

namespace App\Observers;

use App\Models\Ressourcetransaction;
use App\Services\SupabaseSyncService;
use Illuminate\Support\Facades\Log;

class RessourceTransactionObserver
{
    public function created(Ressourcetransaction $transaction)
    {
        app(SupabaseSyncService::class)->pushTransaction($transaction);
        Log::info("✅ Observer RessourceTransactionObserver déclenché created : transaction #{$transaction->id}");
    }

    public function updated(Ressourcetransaction $transaction)
    {
        app(SupabaseSyncService::class)->pushTransaction($transaction);
        Log::info("✅ Observer RessourceTransactionObserver déclenché updated : transaction #{$transaction->id}");
    }
}
