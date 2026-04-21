<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SupabaseSyncService;

class SyncController extends Controller
{
    public function triggerSync(Request $request)
    {
        try {
            Log::info("🚀 Synchronisation Supabase demandée via AJAX", [
                'user_id' => $request->user()->id ?? null,
            ]);

            // Appel du service
            app(SupabaseSyncService::class)->syncAllToSupabase();

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation exécutée avec succès',
            ]);
        } catch (\Throwable $e) {
            Log::error("💥 Erreur pendant la synchro Supabase", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur pendant la synchronisation',
            ], 500);
        }
    }
}
