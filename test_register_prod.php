<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST REGISTER AVEC VALEURS DE PROD ===\n\n";

// Valeurs de production
$supabaseUrl = 'https://odin.cjesnova.com';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0';

echo "URL: " . $supabaseUrl . "\n";
echo "API Key: " . substr($apiKey, 0, 30) . "...\n\n";

// 1. Test inscription directe avec valeurs de prod
echo "1. TEST INSCRIPTION DIRECTE :\n";
$signupResponse = \Illuminate\Support\Facades\Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/signup', [
    'email' => 'testuser' . time() . '@gmail.com', // Email unique
    'password' => 'Yokyokyok1!', // Mot de passe valide
    'data' => ['full_name' => 'Test User Production'],
    'email_confirm' => true,
]);

echo "Status: " . $signupResponse->status() . "\n";
echo "Réponse: " . $signupResponse->body() . "\n\n";

// 2. Test avec SupabaseService (en forçant les bonnes valeurs)
echo "2. TEST AVEC SupabaseService :\n";
try {
    // Temporairement modifier les env
    $_ENV['SUPABASE_URL'] = $supabaseUrl;
    $_ENV['SUPABASE_API_KEY'] = $apiKey;
    
    $supabaseService = new \App\Services\SupabaseService();
    $response = $supabaseService->signUp(
        'testuser' . (time() + 1) . '@gmail.com', // Email unique
        'Yokyokyok1!', // Mot de passe valide
        ['full_name' => 'Test User Service']
    );
    
    echo "Réponse service: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
    if (isset($response['error'])) {
        echo "❌ Erreur service: " . $response['error'] . "\n";
    } elseif (isset($response['id']) || isset($response['user']['id'])) {
        echo "✅ Service inscription réussie !\n";
        
        // Test connexion
        echo "\n3. TEST CONNEXION :\n";
        $loginResponse = \Illuminate\Support\Facades\Http::withHeaders([
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
            'email' => 'testuser' . (time() + 1) . '@gmail.com',
            'password' => 'Yokyokyok1!',
        ]);
        
        echo "Status connexion: " . $loginResponse->status() . "\n";
        if ($loginResponse->successful()) {
            echo "✅ Connexion réussie !\n";
            $loginData = $loginResponse->json();
            echo "User ID: " . ($loginData['user']['id'] ?? 'N/A') . "\n";
        } else {
            echo "❌ Connexion échouée: " . $loginResponse->body() . "\n";
        }
    } else {
        echo "❌ Réponse service inattendue\n";
    }
} catch (Exception $e) {
    echo "❌ Exception service: " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST PROD ===\n";
?>
