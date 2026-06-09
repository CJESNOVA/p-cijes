<?php

/**
 * Teste uniquement l'authentification API utilisée par NeedController::getApiToken()
 *
 * Usage: php scripts/test_need_api_auth.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiUrl = rtrim(config('services.api.url') ?? 'https://api.example.com', '/');
$email = config('services.api.email');
$password = config('services.api.password');
$countryId = config('services.api.country_id', 'TG');

echo "=== Test auth API (NeedController::getApiToken) ===\n";
echo "URL      : {$apiUrl}/api/v1/auth/login\n";
echo "Email    : " . ($email ?: '(non configuré)') . "\n";
echo "Country  : {$countryId}\n\n";

if (!$email || !$password) {
    echo "ECHEC: API_EMAIL ou API_PASSWORD manquant dans .env\n";
    exit(1);
}

$loginResponse = Http::timeout(15)->post("{$apiUrl}/api/v1/auth/login", [
    'email' => $email,
    'password' => $password,
]);

echo "HTTP status : {$loginResponse->status()}\n";

if ($loginResponse->failed()) {
    echo "ECHEC: requête login rejetée\n";
    echo "Body: {$loginResponse->body()}\n";
    exit(1);
}

$body = $loginResponse->json();
echo "Réponse JSON (clés racine) : " . implode(', ', array_keys($body ?? [])) . "\n\n";

// Ce que NeedController attend actuellement
$tokenNeedController = $loginResponse->json('access_token');

// Formats alternatifs possibles (AuthController local)
$tokenData = $loginResponse->json('data.token');
$tokenRoot = $loginResponse->json('token');
$supabaseToken = $loginResponse->json('data.supabase_token');

echo "--- Extraction des tokens ---\n";
echo "access_token (clé attendue par NeedController) : " . ($tokenNeedController ? substr($tokenNeedController, 0, 20) . '...' : 'NULL') . "\n";
echo "data.token (AuthController local)              : " . ($tokenData ? substr($tokenData, 0, 20) . '...' : 'NULL') . "\n";
echo "token (racine)                                 : " . ($tokenRoot ? substr($tokenRoot, 0, 20) . '...' : 'NULL') . "\n";
echo "data.supabase_token                            : " . ($supabaseToken ? substr($supabaseToken, 0, 20) . '...' : 'NULL') . "\n\n";

$token = $tokenNeedController ?? $tokenData ?? $tokenRoot;

if (!$token) {
    echo "ECHEC: aucun token trouvé — getApiToken() lèverait 'Token API manquant'\n";
    echo "Body complet:\n" . json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(1);
}

if (!$tokenNeedController) {
    echo "ATTENTION: NeedController cherche 'access_token' mais le token est ailleurs.\n";
    echo "           getApiToken() échouerait sans correction du code.\n\n";
}

// Vérifier que le token fonctionne sur un endpoint protégé (optionnel)
$meResponse = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json',
    'x-country-id' => $countryId,
])->timeout(15)->get("{$apiUrl}/api/v1/auth/me");

echo "--- Vérification token (GET /api/v1/auth/me) ---\n";
echo "HTTP status : {$meResponse->status()}\n";

if ($meResponse->successful()) {
    echo "SUCCES: authentification OK, token valide.\n";
    $me = $meResponse->json();
    $userEmail = $me['data']['user']['email'] ?? $me['email'] ?? null;
    if ($userEmail) {
        echo "Utilisateur  : {$userEmail}\n";
    }
    exit(0);
}

echo "Token obtenu mais /auth/me a échoué (endpoint peut ne pas exister sur l'API distante).\n";
echo "SUCCES partiel: login OK, token reçu.\n";
echo "Body /me: {$meResponse->body()}\n";
exit(0);
