<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ApiToken;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Authentifie un utilisateur et retourne un token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $response = $this->supabase->login($request->email, $request->password);

        // Debug pour voir la réponse de Supabase
        \Log::info('Supabase login response:', [
            'response' => $response,
            'email' => $request->email
        ]);

        if (isset($response['error'])) {
            return response()->json([
                'success' => false,
                'message' => $response['message'],
                'data' => null
            ], $response['status'] ?? 401);
        }

        // Vérifier que la réponse contient bien les données utilisateur
        if (!isset($response['user'])) {
            return response()->json([
                'success' => false,
                'message' => 'Réponse Supabase invalide: données utilisateur manquantes',
                'data' => null
            ], 500);
        }

        // Récupérer ou créer l'utilisateur local
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $response['user']['user_metadata']['name'] ?? ($response['user']['email'] ?? $request->email),
                'password' => bcrypt($request->password), // Garder un hash local pour compatibilité
                'supabase_user_id' => $response['user']['id'] ?? null,
            ]
        );

        // Supprimer les anciens tokens de l'utilisateur
        ApiToken::where('user_id', $user->id)->delete();

        // Créer un nouveau token pour l'API locale
        $token = Str::random(60);
        $hashedToken = hash('sha256', $token);
        
        ApiToken::create([
            'user_id' => $user->id,
            'token' => $hashedToken,
            'expires_at' => now()->addDays(30), // Expire dans 30 jours
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Authentification réussie',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name ?? null,
                    'supabase_user_id' => $user->supabase_user_id,
                ],
                'token' => $token,
                'supabase_token' => $response['access_token'],
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(30)->toISOString(),
                'supabase_expires_at' => $response['expires_in'] ?? 3600
            ]
        ]);
    }

    /**
     * Déconnecte l'utilisateur (révoque le token)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Récupérer le token et le supprimer localement
        $token = $request->bearerToken();
        if ($token) {
            $hashedToken = hash('sha256', $token);
            ApiToken::where('token', $hashedToken)->delete();
            
            // Tenter de révoquer le token Supabase
            $this->supabase->logout($token);
        }

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Retourne les informations de l'utilisateur authentifié
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name ?? null,
                    'supabase_user_id' => $user->supabase_user_id,
                ]
            ]
        ]);
    }
}
