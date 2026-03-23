<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST SIGNUP DIRECT ===\n\n";

// Forcer les bonnes valeurs
$supabaseUrl = 'https://odin.cjesnova.com';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0';

echo "URL: " . $supabaseUrl . "\n";
echo "API Key: " . substr($apiKey, 0, 30) . "...\n\n";

// Test 1: Sans email_confirm
echo "1. TEST SANS email_confirm :\n";
$response1 = \Illuminate\Support\Facades\Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/signup', [
    'email' => 'testuser' . time() . '@gmail.com',
    'password' => 'Yokyokyok1!',
    'data' => ['full_name' => 'Test User'],
]);

echo "Status: " . $response1->status() . "\n";
echo "Réponse: " . $response1->body() . "\n\n";

// Test 2: Avec email_confirm: false
echo "2. TEST AVEC email_confirm: false :\n";
$response2 = \Illuminate\Support\Facades\Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/signup', [
    'email' => 'testuser' . (time() + 1) . '@gmail.com',
    'password' => 'Yokyokyok1!',
    'data' => ['full_name' => 'Test User 2'],
    'email_confirm' => false,
]);

echo "Status: " . $response2->status() . "\n";
echo "Réponse: " . $response2->body() . "\n\n";

// Test 3: Vérifier si l'utilisateur existe déjà
echo "3. TEST SI UTILISATEUR EXISTE DÉJÀ :\n";
$checkResponse = \Illuminate\Support\Facades\Http::withHeaders([
    'apikey' => $apiKey,
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
    'email' => 'lookyruben@gmail.com',
    'password' => 'Yokyokyok1!',
]);

echo "Status: " . $checkResponse->status() . "\n";
echo "Réponse: " . $checkResponse->body() . "\n\n";

echo "=== FIN TEST ===\n";
?>
