<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DÉBOGAGE REGISTER ===\n\n";

// 1. Test URL générée par signUp()
echo "1. URL GÉNÉRÉE PAR signUp() :\n";
$url = rtrim('https://odin.cjesnova.com', '/') . '/auth/v1/signup'; // URL de prod
echo "URL: " . $url . "\n";
echo "URL correcte ? " . (strpos($url, '//') !== false ? '❌ Double slash détecté' : '✅ OK') . "\n\n";

// 2. Test validation Laravel
echo "2. TEST VALIDATION LARAVEL :\n";
try {
    // Simuler les données du formulaire
    $request = new \Illuminate\Http\Request([
        'name' => 'Test User',
        'email' => 'yokamly@gmail.com',
        'password' => 'Yokyokyok1!',
        'password_confirmation' => 'Yokyokyok1!'
    ]);
    
    $validator = \Validator::make($request->all(), [
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
    
    if ($validator->fails()) {
        echo "❌ Validation échouée :\n";
        foreach ($validator->errors()->all() as $error) {
            echo "- " . $error . "\n";
        }
    } else {
        echo "✅ Validation réussie\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur validation: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Test SupabaseService->signUp()
echo "3. TEST SupabaseService->signUp() :\n";
try {
    $supabaseService = new \App\Services\SupabaseService();
    $response = $supabaseService->signUp(
        'yokamly@gmail.com',
        'Yokyokyok1!',
        ['full_name' => 'Test User']
    );
    
    echo "Réponse: " . json_encode($response) . "\n";
    
    if (isset($response['error'])) {
        echo "❌ Erreur Supabase: " . $response['error'] . "\n";
    } elseif (isset($response['id']) || isset($response['user']['id'])) {
        echo "✅ Inscription Supabase réussie\n";
    } else {
        echo "❌ Réponse inattendue\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DÉBOGAGE ===\n";
?>
