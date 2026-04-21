<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DÉBOGAGE AUTHCONTROLLER AVEC CLÉS PROD ===\n\n";

// Forcer les clés de production
$_ENV['SUPABASE_URL'] = 'https://odin.cjesnova.com';
$_ENV['SUPABASE_API_KEY'] = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6ImFub24iLCJpc3MiOiJzdXBhYmFzZSJ9.pwGqcQclXQ4Y4VHCZwCz9E_LX8nyNI9_VixjZkxBHP0';
$_ENV['SUPABASE_SERVICE_ROLE_KEY'] = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NzA2NDM4MDQsImV4cCI6MTg5MzQ1NjAwMCwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlzcyI6InN1cGFiYXNlIn0.s6dQhoyDPXWWDaE91non2TrB39KT1wro7_IhYvwdsCA';

echo "URL: " . $_ENV['SUPABASE_URL'] . "\n";
echo "API Key: " . substr($_ENV['SUPABASE_API_KEY'], 0, 30) . "...\n\n";

// 1. Simuler le register() du AuthController
echo "1. SIMULATION REGISTER() :\n";

try {
    // Créer un faux request
    $request = new \Illuminate\Http\Request([
        'name' => 'Test User',
        'email' => 'testuser' . time() . '@gmail.com',
        'password' => 'Yokyokyok1!',
        'password_confirmation' => 'Yokyokyok1!'
    ]);

    // Validation (comme dans le AuthController)
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => [
            'required',
            'string',
            'confirmed',
            'min:8',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*?&]/',
        ],
    ]);

    echo "✅ Validation Laravel réussie\n\n";

    // Appel SupabaseService->signUp (comme dans le AuthController)
    $supabaseService = new \App\Services\SupabaseService();
    
    echo "2. APPEL SupabaseService->signUp() :\n";
    $response = $supabaseService->signUp(
        $request->email,
        $request->password,
        ['full_name' => $request->name]
    );

    echo "Réponse brute: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

    // Vérification (comme dans le AuthController)
    $supabaseUser = null;

    if (isset($response['user']['id'])) {
        $supabaseUser = $response['user'];
        echo "✅ Format Supabase Cloud détecté\n";
    } elseif (isset($response['id'])) {
        $supabaseUser = $response;
        echo "✅ Format Supabase Self-hosted détecté\n";
    } else {
        echo "❌ Aucun format détecté\n";
    }

    if ($supabaseUser) {
        echo "✅ Utilisateur Supabase créé: " . $supabaseUser['email'] . "\n";
        echo "User ID: " . $supabaseUser['id'] . "\n";
    } else {
        echo "❌ Création utilisateur échouée\n";
        
        if (isset($response['error'])) {
            echo "Erreur Supabase: " . $response['error'] . "\n";
            echo "Description: " . ($response['error_description'] ?? 'N/A') . "\n";
        }
    }

} catch (\Illuminate\Validation\ValidationException $e) {
    echo "❌ Erreur validation Laravel:\n";
    foreach ($e->errors()->all() as $error) {
        echo "- " . $error . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception générale: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DÉBOGAGE ===\n";
?>
