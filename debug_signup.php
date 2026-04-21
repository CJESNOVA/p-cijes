<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== DÉBOGAGE INSCRIPTION ===\n\n";

$supabaseUrl = 'https://odin.cjesnova.com';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0';

echo "Test d'inscription de lookyruben@gmail.com...\n\n";

// 1. Test inscription directe via Supabase
echo "1. TEST INSCRIPTION DIRECTE SUPABASE :\n";
$signupResponse = Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/signup', [
    'email' => 'lookyruben@gmail.com',
    'password' => 'yokyokyok',
    'data' => ['full_name' => 'Ruben Test'],
    'email_confirm' => true,
]);

echo "Status: " . $signupResponse->status() . "\n";
echo "Réponse: " . $signupResponse->body() . "\n\n";

// 2. Analyser la réponse
$responseData = $signupResponse->json();
if (isset($responseData['error'])) {
    echo "❌ ERREUR INSCRIPTION :\n";
    echo "Code erreur: " . ($responseData['error_code'] ?? 'N/A') . "\n";
    echo "Message: " . ($responseData['msg'] ?? $responseData['error_description'] ?? $responseData['error'] ?? 'N/A') . "\n\n";
    
    // Messages d'erreur spécifiques
    if (strpos($responseData['error'] ?? '', 'user_already_exists') !== false) {
        echo "➡️ L'utilisateur existe déjà ! Essayez de vous connecter.\n\n";
    }
} elseif (isset($responseData['id']) || isset($responseData['user']['id'])) {
    echo "✅ INSCRIPTION RÉUSSIE !\n";
    echo "User ID: " . ($responseData['id'] ?? $responseData['user']['id'] ?? 'N/A') . "\n\n";
    
    // Test de connexion immédiat
    echo "2. TEST CONNEXION IMMÉDIATE :\n";
    $loginResponse = Http::withHeaders([
        'apikey' => $apiKey,
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
        'email' => 'lookyruben@gmail.com',
        'password' => 'yokyokyok',
    ]);

    echo "Status connexion: " . $loginResponse->status() . "\n";
    echo "Réponse connexion: " . $loginResponse->body() . "\n\n";
}

// 3. Vérifier si l'utilisateur existe déjà
echo "3. VÉRIFICATION SI UTILISATEUR EXISTE :\n";
$checkResponse = Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
    'email' => 'lookyruben@gmail.com',
    'password' => 'yokyokyok',
]);

echo "Status vérif: " . $checkResponse->status() . "\n";
if ($checkResponse->status() === 200) {
    echo "✅ L'UTILISATEUR EXISTE DÉJÀ !\n";
    echo "➡️ Essayez de vous connecter avec ces identifiants.\n";
} else {
    echo "❌ L'utilisateur n'existe pas.\n";
}

echo "\n=== FIN DÉBOGAGE ===\n";
?>
