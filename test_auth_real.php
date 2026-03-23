<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TEST AUTHENTIFICATION RÉEL ===\n\n";

// Configuration correcte
$supabaseUrl = 'https://odin.cjesnova.com';  // ✅ Sans slash final
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0';

echo "URL: " . $supabaseUrl . "\n";
echo "Email: lookyruben@gmail.com\n";
echo "API Key: " . substr($apiKey, 0, 30) . "...\n\n";

// Test d'authentification avec vos vrais identifiants
echo "1. TEST LOGIN AVEC VOS IDENTIFIANTS :\n";
$authResponse = Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
    'email' => 'lookyruben@gmail.com',
    'password' => 'yokyokyok',
]);

echo "Status: " . $authResponse->status() . "\n";
echo "Réponse: " . $authResponse->body() . "\n\n";

// Analyse de la réponse
$responseData = $authResponse->json();
if (isset($responseData['access_token'])) {
    echo "✅ CONNEXION RÉUSSIE !\n";
    echo "Access token: " . substr($responseData['access_token'], 0, 30) . "...\n";
    echo "User ID: " . ($responseData['user']['id'] ?? 'N/A') . "\n";
    echo "Email: " . ($responseData['user']['email'] ?? 'N/A') . "\n\n";
    
    // Test de récupération des infos utilisateur avec le token
    echo "2. TEST RÉCUPÉRATION INFOS UTILISATEUR :\n";
    $userResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $responseData['access_token'],
        'apikey' => $apiKey,
    ])->get($supabaseUrl . '/auth/v1/user');
    
    echo "Status: " . $userResponse->status() . "\n";
    echo "Réponse: " . $userResponse->body() . "\n\n";
    
} else {
    echo "❌ CONNEXION ÉCHOUÉE !\n";
    if (isset($responseData['error'])) {
        echo "Erreur: " . $responseData['error'] . "\n";
        echo "Description: " . ($responseData['error_description'] ?? 'N/A') . "\n";
    }
}

echo "\n=== FIN TEST ===\n";
?>
