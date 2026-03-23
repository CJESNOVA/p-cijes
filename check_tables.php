<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== VÉRIFICATION TABLES EXISTANTES ===\n\n";

$supabaseUrl = 'https://odin.cjesnova.com';
$serviceKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlzcyI6InN1cGFiYXNlIn0.s6dQhoyDPXWWDaE91non2TrB39KT1wro7_IhYvwdsCA';

echo "Vérification des tables disponibles...\n\n";

// Lister toutes les tables
$tablesResponse = Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/rest/v1/');

echo "Status: " . $tablesResponse->status() . "\n";
echo "Réponse: " . $tablesResponse->body() . "\n\n";

// Tester quelques tables courantes
$tablesToTest = ['auth.users', 'users', 'profiles', 'accounts', 'countries', 'languages'];

foreach ($tablesToTest as $table) {
    $testResponse = Http::withHeaders([
        'apikey' => $serviceKey,
        'Authorization' => 'Bearer ' . $serviceKey,
        'Content-Type' => 'application/json',
    ])->get($supabaseUrl . '/rest/v1/' . $table . '?limit=1');

    echo "Table '$table': " . $testResponse->status() . " ";
    if ($testResponse->status() === 200) {
        echo "✅ Existe";
    } else {
        echo "❌ N'existe pas";
    }
    echo "\n";
}

echo "\n=== FIN VÉRIFICATION ===\n";
?>
