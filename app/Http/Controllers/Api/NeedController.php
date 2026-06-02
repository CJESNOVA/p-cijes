<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Membre;
use App\Models\Entreprise;

class NeedController extends Controller
{
    public function create()
    {
        $userId = auth()->id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $entreprises = Entreprise::whereHas('entreprisesmembres', function ($q) use ($membre) {
            $q->where('membre_id', $membre->id);
        })->get();

        return view('needs.create', compact('entreprises'));
    }

    public function store(Request $request)
    {
        // VALIDATION
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'entreprise_id' => 'required|exists:entreprises,id',
            'deadline' => 'nullable|date|after:today',
            'profiles' => 'nullable|string|max:500',
            'conditions' => 'nullable|string|max:1000',
            'priority' => 'nullable|integer|between:1,3',
            'file' => 'nullable|file|max:5120',
        ]);

        // AUTHORIZATION CHECK
        $userId = auth()->id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $entreprise = Entreprise::whereHas('entreprisesmembres', function ($q) use ($membre) {
            $q->where('membre_id', $membre->id);
        })->findOrFail($request->entreprise_id);

        $startupId = $entreprise->supabase_startup_id;

        if (!$startupId) {
            Log::warning("Entreprise {$entreprise->id} has no supabase_startup_id");
            return response()->json([
                'success' => false,
                'message' => 'Entreprise non configurée pour l\'API'
            ], 400);
        }

        try {
            // LOGIN API
            $apiUrl = config('services.api.url') ?? 'https://api.example.com';
            $loginResponse = Http::timeout(10)->post(
                "{$apiUrl}/api/v1/auth/login",
                [
                    'email' => config('services.api.email'),
                    'password' => config('services.api.password'),
                ]
            );

            if ($loginResponse->failed()) {
                Log::error('API Login failed', [
                    'status' => $loginResponse->status(),
                    'body' => $loginResponse->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur d\'authentification API'
                ], 500);
            }

            $token = $loginResponse->json('access_token');
            if (!$token) {
                Log::error('No access token received from API');
                return response()->json([
                    'success' => false,
                    'message' => 'Token API manquant'
                ], 500);
            }

            // FILE UPLOAD
            $fileUrl = null;
            if ($request->hasFile('file')) {
                $fileUrl = $request->file('file')->store('needs', 'public');
            }

            // CALL NEED API
            $countryId = config('services.api.country_id', 'TG');
            $needResponse = Http::withHeaders([
                'authorization' => 'Bearer ' . $token,
                'x-country-id' => $countryId,
                'Accept' => 'application/json',
            ])->timeout(10)->post(
                "{$apiUrl}/api/v1/startups/{$startupId}/needs",
                [
                    'category' => $validated['title'],
                    'description' => $validated['description'],
                    'deadline' => $validated['deadline'] ?? null,
                    'profiles' => $validated['profiles'] ?? null,
                    'conditions' => $validated['conditions'] ?? null,
                    'priority' => $validated['priority'] ?? 1,
                    'attachment' => $fileUrl,
                ]
            );

            if ($needResponse->failed()) {
                Log::error('API Need creation failed', [
                    'status' => $needResponse->status(),
                    'body' => $needResponse->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du besoin',
                    'error' => $needResponse->json('message') ?? 'Erreur inconnue'
                ], 500);
            }

            Log::info('Need created successfully', [
                'entreprise_id' => $entreprise->id,
                'user_id' => $userId,
                'api_response' => $needResponse->json()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Besoin publié avec succès',
                'data' => [
                    'need_id' => $needResponse->json('id'),
                    'file_url' => $fileUrl,
                    'api_response' => $needResponse->json()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Exception in NeedController@store', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }
}