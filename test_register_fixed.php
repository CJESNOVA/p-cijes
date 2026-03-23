<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST REGISTER CORRIGÉ ===\n\n";

// 1. Forcer l'URL correcte
$_ENV['SUPABASE_URL'] = 'https://odin.cjesnova.com';

// 2. Test avec mot de passe valide et nouvel email
echo "1. TEST AVEC DONNÉES CORRECTES :\n";

try {
    $supabaseService = new \App\Services\SupabaseService();
    $response = $supabaseService->signUp(
        'testuser' . time() . '@gmail.com', // Email unique
        'Yokyokyok1!', // Mot de passe valide
        ['full_name' => 'Test User']
    );
    
    echo "Réponse: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
    if (isset($response['error'])) {
        echo "❌ Erreur Supabase: " . $response['error'] . "\n";
    } elseif (isset($response['id']) || isset($response['user']['id'])) {
        echo "✅ Inscription Supabase réussie !\n";
        
        // Test connexion immédiate
        echo "\n2. TEST CONNEXION :\n";
        $loginResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_API_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://odin.cjesnova.com/auth/v1/token?grant_type=password', [
            'email' => 'testuser' . time() . '@gmail.com',
            'password' => 'Yokyokyok1!',
        ]);
        
        if ($loginResponse->successful()) {
            echo "✅ Connexion réussie !\n";
        } else {
            echo "❌ Connexion échouée: " . $loginResponse->body() . "\n";
        }
    } else {
        echo "❌ Réponse inattendue\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST ===\n";
?>
