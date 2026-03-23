<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TEST COMPLET SUPABASE (cjes-master) ===\n\n";

// 1. Test de connexion à l'authentification
echo "1. TEST AUTHENTIFICATION (login) :\n";
$authResponse = Http::withHeaders([
    'apikey' => env('SUPABASE_API_KEY'),
    'Authorization' => 'Bearer ' . env('SUPABASE_API_KEY'),
    'Content-Type' => 'application/json',
])->post(env('SUPABASE_URL') . '/auth/v1/token?grant_type=password', [
    'email' => 'test@test.com',
    'password' => 'testpassword123',
]);

echo "Status: " . $authResponse->status() . "\n";
echo "Réponse: " . $authResponse->body() . "\n\n";

// 2. Test de lecture de la table pays avec SERVICE_ROLE_KEY
echo "2. TEST LECTURE PAYS (countries) avec SERVICE_ROLE_KEY :\n";
$paysResponse = Http::withHeaders([
    'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
    'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
    'Content-Type' => 'application/json',
])->get(env('SUPABASE_URL') . '/rest/v1/countries?limit=5');

echo "Status: " . $paysResponse->status() . "\n";
echo "Nombre de pays: " . count($paysResponse->json()) . "\n";
echo "Réponse: " . $paysResponse->body() . "\n\n";

// 3. Test de lecture de la table langues avec SERVICE_ROLE_KEY
echo "3. TEST LECTURE LANGUES (languages) avec SERVICE_ROLE_KEY :\n";
$languesResponse = Http::withHeaders([
    'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
    'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
    'Content-Type' => 'application/json',
])->get(env('SUPABASE_URL') . '/rest/v1/languages?limit=5');

echo "Status: " . $languesResponse->status() . "\n";
echo "Nombre de langues: " . count($languesResponse->json()) . "\n";
echo "Réponse: " . $languesResponse->body() . "\n\n";

// 4. Test avec SupabaseService (la classe)
echo "4. TEST AVEC SupabaseService :\n";
try {
    $supabaseService = new App\Services\SupabaseService();
    
    // Test get countries
    echo "4a. SupabaseService->get('countries') :\n";
    $countries = $supabaseService->get('countries', [], true); // true = useServiceRole
    echo "Status: " . (is_array($countries) ? '✅ Succès' : '❌ Erreur') . "\n";
    echo "Nombre de pays: " . (is_array($countries) ? count($countries) : 0) . "\n\n";
    
    // Test get languages
    echo "4b. SupabaseService->get('languages') :\n";
    $languages = $supabaseService->get('languages', [], true); // true = useServiceRole
    echo "Status: " . (is_array($languages) ? '✅ Succès' : '❌ Erreur') . "\n";
    echo "Nombre de langues: " . (is_array($languages) ? count($languages) : 0) . "\n\n";
    
} catch (Exception $e) {
    echo "❌ Erreur SupabaseService: " . $e->getMessage() . "\n\n";
}

// 5. Test avec les modèles
echo "5. TEST AVEC MODÈLES :\n";
try {
    $paysModel = new App\Models\Pays();
    $allPays = $paysModel->all();
    echo "5a. Pays::all() : " . (is_array($allPays) ? count($allPays) . ' pays' : '❌ Erreur') . "\n";
    
    // Vérifier si le modèle Langue existe
    if (class_exists('App\Models\Langue')) {
        $langueModel = new App\Models\Langue();
        $allLangues = $langueModel->all();
        echo "5b. Langue::all() : " . (is_array($allLangues) ? count($allLangues) . ' langues' : '❌ Erreur') . "\n";
    } else {
        echo "5b. Modèle Langue n'existe pas dans ce projet\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur modèles: " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST COMPLET ===\n";
?>
