<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST SIGNUP ADMIN (SERVICE ROLE) ===\n\n";

// Utiliser la clé de service pour contourner la confirmation email
$supabaseUrl = 'https://odin.cjesnova.com';
$serviceKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlzcyI6InN1cGFiYXNlIn0.s6dQhoyDPXWWDaE91non2TrB39KT1wro7_IhYvwdsCA';

echo "URL: " . $supabaseUrl . "\n";
echo "Service Key: " . substr($serviceKey, 0, 30) . "...\n\n";

// Test avec SERVICE_ROLE_KEY (bypass les restrictions)
echo "1. TEST AVEC SERVICE_ROLE_KEY :\n";
$response = \Illuminate\Support\Facades\Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/admin/users', [
    'email' => 'testuser' . time() . '@gmail.com',
    'password' => 'Yokyokyok1!',
    'email_confirm' => false,
    'user_metadata' => ['full_name' => 'Test User Admin'],
]);

echo "Status: " . $response->status() . "\n";
echo "Réponse: " . $response->body() . "\n\n";

// Test si ça marche, on met à jour le SupabaseService
if ($response->successful()) {
    echo "✅ ADMIN SIGNUP RÉUSSI !\n";
    echo "➡️ Il faut utiliser l'endpoint admin/users avec SERVICE_ROLE_KEY\n";
    
    // Test connexion après création
    $userData = $response->json();
    if (isset($userData['id'])) {
        echo "\n2. TEST CONNEXION APRÈS CRÉATION :\n";
        $loginResponse = \Illuminate\Support\Facades\Http::withHeaders([
            'apikey' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0',
            'Content-Type' => 'application/json',
        ])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
            'email' => 'testuser' . (time() - 1) . '@gmail.com',
            'password' => 'Yokyokyok1!',
        ]);
        
        echo "Status connexion: " . $loginResponse->status() . "\n";
        echo "Réponse connexion: " . $loginResponse->body() . "\n";
    }
} else {
    echo "❌ ADMIN SIGNUP ÉCHOUÉ AUSSI\n";
}

echo "\n=== FIN TEST ===\n";
?>
