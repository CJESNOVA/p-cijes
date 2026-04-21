<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

echo "=== TEST DE CONNEXION AVEC DEBUG ===\n\n";

// Test de connexion avec vos identifiants locaux
$email = 'yokamly@gmail.com';
$password = 'yokyokyok';

echo "Email: " . $email . "\n";
echo "URL Supabase: " . env('SUPABASE_URL') . "\n";
echo "URL Auth: " . rtrim(env('SUPABASE_URL'), '/') . '/auth/v1' . "\n\n";

try {
    $supabaseService = new \App\Services\SupabaseService();
    
    echo "2. TEST signIn() avec SupabaseService :\n";
    $result = $supabaseService->signIn($email, $password);
    
    echo "Résultat : " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
} catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DU TEST ===\n";
