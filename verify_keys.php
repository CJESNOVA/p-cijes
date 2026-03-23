<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== VÉRIFICATION CLÉS SUPABASE ===\n\n";

$supabaseUrl = 'https://odin.cjesnova.com';

// Clés du .env
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0';
$serviceKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlzcyI6InN1cGFiYXNlIn0.s6dQhoyDPXWWDaE91non2TrB39KT1wro7_IhYvwdsCA';

echo "URL: " . $supabaseUrl . "\n\n";

// 1. Test API_KEY (anon)
echo "1. TEST API_KEY (anon) :\n";
$apiTest = Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/rest/v1/countries?limit=1');

echo "Status: " . $apiTest->status() . "\n";
echo "Résultat: " . ($apiTest->successful() ? '✅ Valide' : '❌ Invalide') . "\n\n";

// 2. Test SERVICE_ROLE_KEY
echo "2. TEST SERVICE_ROLE_KEY :\n";
$serviceTest = Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/rest/v1/countries?limit=1');

echo "Status: " . $serviceTest->status() . "\n";
echo "Résultat: " . ($serviceTest->successful() ? '✅ Valide' : '❌ Invalide') . "\n\n";

// 3. Test endpoint d'authentification
echo "3. TEST ENDPOINT AUTH :\n";
$authEndpointTest = Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/auth/v1/settings');

echo "Status: " . $authEndpointTest->status() . "\n";
echo "Résultat: " . ($authEndpointTest->successful() ? '✅ Endpoint auth OK' : '❌ Endpoint auth KO') . "\n\n";

// 4. Décoder les JWT pour vérifier
echo "4. DÉCODAGE JWT :\n";
$apiParts = explode('.', $apiKey);
$serviceParts = explode('.', $serviceKey);

if (count($apiParts) >= 2) {
    $apiPayload = json_decode(base64_decode($apiParts[1]), true);
    echo "API_KEY - Rôle: " . ($apiPayload['role'] ?? 'N/A') . "\n";
    echo "API_KEY - Expire: " . date('Y-m-d H:i:s', $apiPayload['exp'] ?? 0) . "\n";
}

if (count($serviceParts) >= 2) {
    $servicePayload = json_decode(base64_decode($serviceParts[1]), true);
    echo "SERVICE_KEY - Rôle: " . ($servicePayload['role'] ?? 'N/A') . "\n";
    echo "SERVICE_KEY - Expire: " . date('Y-m-d H:i:s', $servicePayload['exp'] ?? 0) . "\n";
}

echo "\n=== FIN VÉRIFICATION ===\n";
?>
