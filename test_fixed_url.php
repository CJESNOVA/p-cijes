<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TEST AVEC URL CORRIGÉE ===\n\n";

// URL correcte (sans slash final)
$supabaseUrl = 'https://odin.cjesnova.com';  // ✅ Sans slash final
$serviceKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlzcyI6InN1cGFiYXNlIn0.s6dQhoyDPXWWDaE91non2TrB39KT1wro7_IhYvwdsCA';

echo "URL corrigée: " . $supabaseUrl . "\n";
echo "Service Role Key: " . substr($serviceKey, 0, 30) . "...\n\n";

// 1. Test lecture countries
echo "1. TEST LECTURE COUNTRIES :\n";
$countriesResponse = Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/rest/v1/countries?limit=3');

echo "Status: " . $countriesResponse->status() . "\n";
echo "Réponse: " . $countriesResponse->body() . "\n\n";

// 2. Test lecture languages
echo "2. TEST LECTURE LANGUAGES :\n";
$languagesResponse = Http::withHeaders([
    'apikey' => $serviceKey,
    'Authorization' => 'Bearer ' . $serviceKey,
    'Content-Type' => 'application/json',
])->get($supabaseUrl . '/rest/v1/languages?limit=3');

echo "Status: " . $languagesResponse->status() . "\n";
echo "Réponse: " . $languagesResponse->body() . "\n\n";

// 3. Test authentification
echo "3. TEST AUTHENTIFICATION :\n";
$authResponse = Http::withHeaders([
    'apikey' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0',
    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0',
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
    'email' => 'test@test.com',
    'password' => 'testpassword123',
]);

echo "Status: " . $authResponse->status() . "\n";
echo "Réponse: " . $authResponse->body() . "\n\n";

echo "=== FIN TEST ===\n";
?>
