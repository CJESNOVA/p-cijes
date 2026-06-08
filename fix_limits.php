<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Action;

echo "=== CORRECTION DES LIMITES ===\n\n";

// Augmenter la limite de INSCRIPTION pour permettre plus d'inscriptions
$action = Action::where('code', 'INSCRIPTION')->first();
if ($action) {
    echo "Action INSCRIPTION actuelle :\n";
    echo "  Limite : {$action->limite}\n";
    echo "  Utilisée : " . \App\Models\Recompense::where('action_id', $action->id)->count() . "\n";
    
    // Augmenter la limite à 1000 (pratiquement illimité)
    $action->limite = 1000;
    $action->save();
    
    echo "\n✅ Limite augmentée à 1000\n";
    echo "   Les nouveaux membres pourront recevoir leur récompense d'inscription\n";
} else {
    echo "❌ Action INSCRIPTION non trouvée\n";
}

// Faire de même pour PROFIL_COMPLET
$action = Action::where('code', 'PROFIL_COMPLET')->first();
if ($action) {
    echo "\nAction PROFIL_COMPLET actuelle :\n";
    echo "  Limite : {$action->limite}\n";
    echo "  Utilisée : " . \App\Models\Recompense::where('action_id', $action->id)->count() . "\n";
    
    $action->limite = 1000;
    $action->save();
    
    echo "✅ Limite augmentée à 1000\n";
}

echo "\n=== TERMINÉ ===\n";
