<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== CRÉATION UTILISATEUR CORRECTE ===\n\n";

$supabaseUrl = 'https://odin.cjesnova.com';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0';

echo "Création de l'utilisateur lookyruben@gmail.com...\n";

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

$responseData = $signupResponse->json();
if (isset($responseData['id']) || isset($responseData['user']['id'])) {
    echo "✅ UTILISATEUR CRÉÉ AVEC SUCCÈS !\n";
    
    // Attendre un peu puis tester la connexion
    sleep(2);
    
    echo "\nTest de connexion immédiat...\n";
    $authResponse = Http::withHeaders([
        'apikey' => $apiKey,
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
        'email' => 'lookyruben@gmail.com',
        'password' => 'yokyokyok',
    ]);

    echo "Status connexion: " . $authResponse->status() . "\n";
    echo "Réponse connexion: " . $authResponse->body() . "\n";
    
} else {
    echo "❌ CRÉATION ÉCHOUÉE\n";
    if (isset($responseData['error'])) {
        echo "Erreur: " . $responseData['error'] . "\n";
        echo "Description: " . ($responseData['error_description'] ?? 'N/A') . "\n";
    }
}

echo "\n=== FIN TEST ===\n";
?>
