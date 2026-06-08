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
    /**
     * Obtenir le token d'authentification de l'API
     */
    private function getApiToken()
    {
        $apiUrl = config('services.api.url') ?? 'https://api.example.com';
        
        $loginResponse = Http::timeout(10)->post(
            "{$apiUrl}/api/v1/auth/login",
            [
                'email' => config('services.api.email'),
                'password' => config('services.api.password'),
            ]
        );

        if ($loginResponse->failed()) {
            Log::error('Erreur de connexion API', [
                'status' => $loginResponse->status(),
                'body' => $loginResponse->body()
            ]);
            throw new \Exception('Erreur d\'authentification API');
        }

        $token = $loginResponse->json('access_token');
        if (!$token) {
            Log::error('Aucun token d\'accès reçu de l\'API');
            throw new \Exception('Token API manquant');
        }

        return $token;
    }

    /**
     * Obtenir les en-têtes pour les requêtes API
     */
    private function getApiHeaders($token)
    {
        return [
            'authorization' => 'Bearer ' . $token,
            'x-country-id' => config('services.api.country_id', 'TG'),
            'Accept' => 'application/json',
        ];
    }

    public function create()
    {
        $userId = auth()->id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $entreprises = Entreprise::whereHas('entreprisesmembres', function ($q) use ($membre) {
            $q->where('membre_id', $membre->id);
        })->get();

        return view('needs.create', compact('entreprises'));
    }

    /**
     * Lister tous les besoins PME
     * GET /api/v1/pme-needs
     */
    public function index()
    {
        try {
            $apiUrl = config('services.api.url') ?? 'https://api.example.com';
            $token = $this->getApiToken();

            $response = Http::withHeaders($this->getApiHeaders($token))
                ->timeout(10)
                ->get("{$apiUrl}/api/v1/pme-needs");

            if ($response->failed()) {
                Log::error('Erreur lors de la récupération des besoins', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Erreur lors de la récupération des besoins'], $response->status());
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Exception dans NeedController@index', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Créer un besoin PME
     * POST /api/v1/pme-needs
     */
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
            return redirect()->back()->with('error', 'Entreprise non configurée pour l\'API');
        }

        try {
            $apiUrl = config('services.api.url') ?? 'https://api.example.com';
            $token = $this->getApiToken();

            // FILE UPLOAD
            $fileUrl = null;
            if ($request->hasFile('file')) {
                $fileUrl = $request->file('file')->store('needs', 'public');
            }

            // APPEL À LA NOUVELLE API DE BESOINS
            $needResponse = Http::withHeaders($this->getApiHeaders($token))
                ->timeout(10)
                ->post(
                    "{$apiUrl}/api/v1/pme-needs",
                    [
                        'category' => $validated['title'],
                        'description' => $validated['description'],
                        'deadline' => $validated['deadline'] ?? null,
                        'profiles' => $validated['profiles'] ?? null,
                        'conditions' => $validated['conditions'] ?? null,
                        'priority' => $validated['priority'] ?? 1,
                        'attachment' => $fileUrl,
                        'startup_id' => $startupId,
                    ]
                );

            if ($needResponse->failed()) {
                Log::error('Erreur lors de la création du besoin', [
                    'status' => $needResponse->status(),
                    'body' => $needResponse->body()
                ]);
                return redirect()->back()->with('error', 'Erreur lors de la création du besoin : ' . ($needResponse->json('message') ?? 'Erreur inconnue'));
            }

            Log::info('Besoin créé avec succès', [
                'entreprise_id' => $entreprise->id,
                'user_id' => $userId,
                'api_response' => $needResponse->json()
            ]);

            return redirect()->back()->with('success', 'Besoin publié avec succès !');

        } catch (\Exception $e) {
            Log::error('Exception dans NeedController@store', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Obtenir un seul besoin PME
     * GET /api/v1/pme-needs/{need_id}
     */
    public function show($needId)
    {
        try {
            $apiUrl = config('services.api.url') ?? 'https://api.example.com';
            $token = $this->getApiToken();

            $response = Http::withHeaders($this->getApiHeaders($token))
                ->timeout(10)
                ->get("{$apiUrl}/api/v1/pme-needs/{$needId}");

            if ($response->failed()) {
                Log::error('Erreur lors de la récupération du besoin', [
                    'need_id' => $needId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Besoin non trouvé'], $response->status());
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Exception dans NeedController@show', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Créer une candidature pour un besoin
     * POST /api/v1/pme-needs/{need_id}/applications
     */
    public function storeApplication(Request $request, $needId)
    {
        $validated = $request->validate([
            'applicant_id' => 'required|string',
            'message' => 'nullable|string',
            'portfolio_url' => 'nullable|url',
            'expected_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $apiUrl = config('services.api.url') ?? 'https://api.example.com';
            $token = $this->getApiToken();

            $response = Http::withHeaders($this->getApiHeaders($token))
                ->timeout(10)
                ->post(
                    "{$apiUrl}/api/v1/pme-needs/{$needId}/applications",
                    $validated
                );

            if ($response->failed()) {
                Log::error('Erreur lors de la création de la candidature', [
                    'need_id' => $needId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Erreur lors de la création de la candidature'], $response->status());
            }

            Log::info('Candidature créée avec succès', [
                'need_id' => $needId,
                'api_response' => $response->json()
            ]);

            return response()->json($response->json(), 201);

        } catch (\Exception $e) {
            Log::error('Exception dans NeedController@storeApplication', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lister les candidatures pour un besoin
     * GET /api/v1/pme-needs/{need_id}/applications
     */
    public function listApplications($needId)
    {
        try {
            $apiUrl = config('services.api.url') ?? 'https://api.example.com';
            $token = $this->getApiToken();

            $response = Http::withHeaders($this->getApiHeaders($token))
                ->timeout(10)
                ->get("{$apiUrl}/api/v1/pme-needs/{$needId}/applications");

            if ($response->failed()) {
                Log::error('Erreur lors de la récupération des candidatures', [
                    'need_id' => $needId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Erreur lors de la récupération des candidatures'], $response->status());
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Exception dans NeedController@listApplications', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Attribuer une candidature
     * PUT /api/v1/pme-needs/{need_id}/applications/{application_id}/award
     */
    public function awardApplication(Request $request, $needId, $applicationId)
    {
        $validated = $request->validate([
            'awarded_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $apiUrl = config('services.api.url') ?? 'https://api.example.com';
            $token = $this->getApiToken();

            $response = Http::withHeaders($this->getApiHeaders($token))
                ->timeout(10)
                ->put(
                    "{$apiUrl}/api/v1/pme-needs/{$needId}/applications/{$applicationId}/award",
                    $validated
                );

            if ($response->failed()) {
                Log::error('Erreur lors de l\'attribution de la candidature', [
                    'need_id' => $needId,
                    'application_id' => $applicationId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Erreur lors de l\'attribution de la candidature'], $response->status());
            }

            Log::info('Candidature attribuée avec succès', [
                'need_id' => $needId,
                'application_id' => $applicationId,
                'api_response' => $response->json()
            ]);

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Exception dans NeedController@awardApplication', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }
}