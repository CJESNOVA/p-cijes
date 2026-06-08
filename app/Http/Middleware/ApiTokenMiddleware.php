<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token manquant',
                'data' => null
            ], 401);
        }

        // Hash du token pour comparaison avec la base de données
        $hashedToken = hash('sha256', $token);
        
        $apiToken = ApiToken::where('token', $hashedToken)
            ->with('user')
            ->first();

        if (!$apiToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide',
                'data' => null
            ], 401);
        }

        if ($apiToken->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Token expiré',
                'data' => null
            ], 401);
        }

        // Ajouter l'utilisateur authentifié à la requête
        $request->setUserResolver(function () use ($apiToken) {
            return $apiToken->user;
        });

        // Ajouter le token à la requête pour utilisation ultérieure
        $request->attributes->set('api_token', $apiToken);

        return $next($request);
    }
}
