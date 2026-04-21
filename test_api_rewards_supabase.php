<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialiser l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

echo "=== TEST DE L'API DE RÉCOMPENSES (Version Supabase) ===\n\n";

// Configuration de l'URL de base
$baseUrl = 'http://localhost:8000/api/rewards';

// 1. Test : Lister les actions disponibles
echo "1. 📋 Test : Lister les actions disponibles\n";
echo "   URL: GET {$baseUrl}/actions\n";

try {
    $response = Http::get("{$baseUrl}/actions");
    
    if ($response->successful()) {
        echo "   ✅ Succès ({$response->status()})\n";
        $actions = $response->json('data');
        echo "   📊 " . count($actions) . " actions trouvées:\n";
        
        foreach ($actions as $action) {
            echo "      - {$action['code']}: {$action['titre']} ({$action['point']} pts, {$action['seuil']})\n";
        }
    } else {
        echo "   ❌ Erreur ({$response->status()}): " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Récupérer un supabase_user_id de test
echo "2. 🔍 Recherche d'un supabase_user_id de test\n";
$testUser = DB::table('users')
    ->whereNotNull('supabase_user_id')
    ->first();

if ($testUser) {
    echo "   ✅ Utilisateur trouvé: {$testUser->name} (supabase_user_id: {$testUser->supabase_user_id})\n";
    $supabaseUserId = $testUser->supabase_user_id;
} else {
    echo "   ⚠️ Aucun utilisateur avec supabase_user_id trouvé. Utilisation d'un ID de test.\n";
    $supabaseUserId = 'test_supabase_user_123';
}

echo "\n";

// 3. Test : Attribution simple de récompense (sans montant)
echo "3. 🎁 Test : Attribution simple de récompense\n";
echo "   URL: POST {$baseUrl}/attribute\n";

$payload1 = [
    'action_code' => 'DEMANDE_SIKA',
    'supabase_user_id' => $supabaseUserId,
    'description' => 'Test API - Demande SIKA'
];

echo "   Payload: " . json_encode($payload1, JSON_PRETTY_PRINT) . "\n";

try {
    $response = Http::post("{$baseUrl}/attribute", $payload1);
    
    if ($response->successful()) {
        echo "   ✅ Succès ({$response->status()})\n";
        $data = $response->json('data');
        echo "   🎯 Récompense ID: {$data['recompense_id']}\n";
        echo "   💰 Points: {$data['recompense_points']}\n";
        echo "   👤 Membre: {$data['membre_info']['nom']} {$data['membre_info']['prenom']}\n";
    } else {
        echo "   ❌ Erreur ({$response->status()}): " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Test : Attribution avec montant (crée transaction)
echo "4. 💳 Test : Attribution avec montant (crée transaction)\n";
echo "   URL: POST {$baseUrl}/attribute\n";

$payload2 = [
    'action_code' => 'RECEPTION_SIKA',
    'supabase_user_id' => $supabaseUserId,
    'montant' => 10000,
    'reference' => 'TEST-API-' . time(),
    'description' => 'Test API - Réception SIKA avec montant'
];

echo "   Payload: " . json_encode($payload2, JSON_PRETTY_PRINT) . "\n";

try {
    $response = Http::post("{$baseUrl}/attribute", $payload2);
    
    if ($response->successful()) {
        echo "   ✅ Succès ({$response->status()})\n";
        $data = $response->json('data');
        echo "   🎯 Récompense ID: {$data['recompense_id']}\n";
        echo "   💰 Points: {$data['recompense_points']}\n";
        echo "   📄 Transaction ID: " . ($data['transaction_id'] ?? 'N/A') . "\n";
        echo "   💵 Solde mis à jour: " . ($data['solde_mis_a_jour'] ? 'Oui' : 'Non') . "\n";
        echo "   📊 Calcul attendu: 25% de 10000 = 2500 points\n";
    } else {
        echo "   ❌ Erreur ({$response->status()}): " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Test : Vérifier les récompenses d'un membre
echo "5. 🔍 Test : Vérifier les récompenses d'un membre\n";
echo "   URL: GET {$baseUrl}/member/rewards?supabase_user_id={$supabaseUserId}\n";

try {
    $response = Http::get("{$baseUrl}/member/rewards", ['supabase_user_id' => $supabaseUserId]);
    
    if ($response->successful()) {
        echo "   ✅ Succès ({$response->status()})\n";
        $data = $response->json('data');
        echo "   👤 Membre: {$data['membre']['nom']} {$data['membre']['prenom']}\n";
        echo "   📊 Total points: {$data['total_points']}\n";
        echo "   🎯 Nombre récompenses: {$data['nombre_recompenses']}\n";
        
        echo "   📋 Dernières récompenses:\n";
        foreach (array_slice($data['recompenses'], 0, 3) as $reward) {
            echo "      - {$reward['action_code']}: {$reward['valeur']} pts ({$reward['created_at']})\n";
        }
    } else {
        echo "   ❌ Erreur ({$response->status()}): " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// 6. Test : Erreur de validation
echo "6. ⚠️ Test : Erreur de validation\n";
echo "   URL: POST {$baseUrl}/attribute\n";

$payload3 = [
    'action_code' => '', // Vide
    'supabase_user_id' => 'inexistant_12345', // Inexistant
];

echo "   Payload: " . json_encode($payload3, JSON_PRETTY_PRINT) . "\n";

try {
    $response = Http::post("{$baseUrl}/attribute", $payload3);
    
    if ($response->status() === 422) {
        echo "   ✅ Erreur de validation détectée (422)\n";
        $errors = $response->json('errors');
        foreach ($errors as $field => $messages) {
            echo "      - {$field}: " . implode(', ', $messages) . "\n";
        }
    } else {
        echo "   ⚠️ Réponse inattendue ({$response->status()}): " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// 7. Test : Avec entreprise (si disponible)
echo "7. 🏢 Test : Attribution avec entreprise\n";

$testStartup = DB::table('entreprises')
    ->whereNotNull('supabase_startup_id')
    ->first();

if ($testStartup) {
    echo "   ✅ Entreprise trouvée: {$testStartup->nom} (supabase_startup_id: {$testStartup->supabase_startup_id})\n";
    
    $payload4 = [
        'action_code' => 'RECEPTION_SIKA',
        'supabase_user_id' => $supabaseUserId,
        'supabase_startup_id' => $testStartup->supabase_startup_id,
        'montant' => 5000,
        'reference' => 'TEST-ENTREPRISE-' . time(),
        'description' => 'Test API avec entreprise'
    ];

    echo "   Payload: " . json_encode($payload4, JSON_PRETTY_PRINT) . "\n";

    try {
        $response = Http::post("{$baseUrl}/attribute", $payload4);
        
        if ($response->successful()) {
            echo "   ✅ Succès ({$response->status()})\n";
            $data = $response->json('data');
            echo "   🎯 Récompense ID: {$data['recompense_id']}\n";
            echo "   🏢 Entreprise: " . ($data['entreprise_info']['nom'] ?? 'N/A') . "\n";
            echo "   💰 Points: {$data['recompense_points']}\n";
        } else {
            echo "   ❌ Erreur ({$response->status()}): " . $response->body() . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠️ Aucune entreprise avec supabase_startup_id trouvée. Test ignoré.\n";
}

echo "\n=== FIN DES TESTS ===\n";
echo "💡 Note: Assurez-vous que le serveur Laravel est en cours d'exécution sur http://localhost:8000\n";
echo "💡 Lancez avec: php artisan serve --port=8000\n";
echo "🔄 L'API utilise maintenant supabase_user_id et supabase_startup_id\n";
