<?php

namespace App\Observers;

use App\Models\Ressourcecompte;
use App\Services\SupabaseSyncService;
use Illuminate\Support\Facades\Log;

class RessourceCompteObserver
{
    public function created(Ressourcecompte $compte)
    {
        app(SupabaseSyncService::class)->pushAccount($compte);
        Log::info("✅ Observer RessourceCompteObserver déclenché created : compte #{$compte->id}");
    }

    public function creating(Ressourcecompte $compte)
    {
        app(SupabaseSyncService::class)->pushAccount($compte);
        Log::info("✅ Observer RessourceCompteObserver déclenché creating : compte #{$compte->id}");
    }

    public function updated(Ressourcecompte $compte)
    {
        app(SupabaseSyncService::class)->pushAccount($compte);
        Log::info("✅ Observer RessourceCompteObserver déclenché updated : compte #{$compte->id}");
    }

    public function saved(Ressourcecompte $compte)
    {
        app(SupabaseSyncService::class)->pushAccount($compte);
        Log::info("🔁 Observer RessourceCompteObserver déclenché saved : compte #{$compte->id}");
    }

    public function saving(Ressourcecompte $compte)
    {
        app(SupabaseSyncService::class)->pushAccount($compte);
        Log::info("🔁 Observer RessourceCompteObserver déclenché saving : compte #{$compte->id}");
    }
}
