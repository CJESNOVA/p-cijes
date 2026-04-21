<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseSyncService;
use Illuminate\Support\Facades\Log;

class RessourceSyncController extends Controller
{
    /**
     * 🔁 Lance la synchronisation manuelle des comptes & transactions vers Supabase
     */
    public function syncToSupabase(SupabaseSyncService $syncService)
    {
        try {
            Log::info("🚀 Synchronisation manuelle vers Supabase déclenchée par utilisateur #" . auth()->id());

            $syncService->syncAllToSupabase();

            return redirect()
                ->back()
                ->with('success', '✅ Synchronisation Supabase lancée avec succès. Consultez les logs pour le détail.');
        } catch (\Exception $e) {
            Log::error("💥 Échec lors du lancement de la synchro Supabase", [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', '❌ Une erreur est survenue pendant la synchronisation.');
        }
    }
}
