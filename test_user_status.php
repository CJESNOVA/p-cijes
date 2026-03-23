<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== VÉRIFICATION STATUT UTILISATEUR ===\n\n";

$supabaseUrl = 'https://odin.cjesnova.com';
$serviceKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlzcyI6InN1cGFiYXNlIn0.s6dQhoyDPXWWDaE91non2TrB39KT1wro7_IhYvwdsCA';

echo "Recherche de l'utilisateur lookyruben@gmail.com...\n\n";

// 1. Chercher l'utilisateur dans la table auth.users
echo "1. RECHERCHE DANS auth.users :\n";
$userResponse = Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/rest/v1/auth.users?email=eq.lookyruben@gmail.com');

echo "Status: " . $userResponse->status() . "\n";
echo "Réponse: " . $userResponse->body() . "\n\n";

// 2. Chercher dans la table users (si elle existe)
echo "2. RECHERCHE DANS users :\n";
$usersResponse = Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/rest/v1/users?email=eq.lookyruben@gmail.com');

echo "Status: " . $usersResponse->status() . "\n";
echo "Réponse: " . $usersResponse->body() . "\n\n";

// 3. Tenter de réinitialiser le mot de passe
echo "3. TEST RÉINITIALISATION MOT DE PASSE :\n";
$resetResponse = Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/recover', [
    'email' => 'lookyruben@gmail.com',
]);

echo "Status: " . $resetResponse->status() . "\n";
echo "Réponse: " . $resetResponse->body() . "\n\n";

// 4. Tenter la connexion avec différents mots de passe courants
$passwords = ['yokyokyok', 'password', '123456', 'admin'];

echo "4. TEST CONNEXION AVEC MOTS DE PASSE COURANTS :\n";
foreach ($passwords as $pwd) {
    $authResponse = Http::withHeaders([
        'apikey' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0',
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0',
        'Content-Type' => 'application/json',
    ])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
        'email' => 'lookyruben@gmail.com',
        'password' => $pwd,
    ]);

    if ($authResponse->status() === 200) {
        echo "✅ CONNEXION RÉUSSIE avec mot de passe: '$pwd'\n";
        break;
    } else {
        echo "❌ Échec avec mot de passe: '$pwd'\n";
    }
}

echo "\n=== FIN VÉRIFICATION ===\n";
?>
