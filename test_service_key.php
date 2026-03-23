<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TEST AVEC SERVICE_ROLE_KEY ===\n\n";

// La clé que vous avez montrée
$serviceKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlzcyI6InN1cGFiYXNlIn0.s6dQhoyDPXWWDaE91non2TrB39KT1wro7_IhYvwdsCA';

// Test avec une URL Supabase générique (à remplacer)
$supabaseUrl = env('SUPABASE_URL');

echo "URL Supabase: " . $supabaseUrl . "\n";
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

// 3. Test avec SupabaseService
echo "3. TEST AVEC SupabaseService :\n";
try {
    $supabaseService = new \App\Services\SupabaseService();
    
    // Forcer l'utilisation de la clé de service
    $countries = $supabaseService->get('countries', [], true);
    echo "3a. Countries via SupabaseService: " . (is_array($countries) ? count($countries) . ' pays' : '❌ Erreur') . "\n";
    
    $languages = $supabaseService->get('languages', [], true);
    echo "3b. Languages via SupabaseService: " . (is_array($languages) ? count($languages) . ' langues' : '❌ Erreur') . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur SupabaseService: " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST ===\n";
?>
