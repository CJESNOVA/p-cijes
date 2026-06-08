<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Membre;
use App\Models\Recompense;
use App\Models\Action;

echo "=== VÉRIFICATION RAPIDE PRODUCTION ===\n\n";

// 1. Vérifier le membre 18
$membre = Membre::find(18);
if ($membre) {
    echo "✅ Membre 18 trouvé : {$membre->prenom} {$membre->nom}\n";
    $rewards = Recompense::where('membre_id', 18)->count();
    echo "   Récompenses : {$rewards}\n";
} else {
    echo "❌ Membre 18 NON TROUVÉ\n";
    echo "   Membres disponibles :\n";
    $membres = Membre::limit(5)->get();
    foreach ($membres as $m) {
        echo "   - ID {$m->id}: {$m->prenom} {$m->nom}\n";
    }
}

// 2. Vérifier l'action INSCRIPTION
$action = Action::where('code', 'INSCRIPTION')->first();
if ($action) {
    echo "\n✅ Action INSCRIPTION trouvée\n";
    $totalUsed = Recompense::where('action_id', $action->id)->count();
    echo "   Limite : {$action->limite}\n";
    echo "   Utilisée globalement : {$totalUsed}\n";
    
    if ($totalUsed >= $action->limite) {
        echo "   ❌ LIMITE GLOBALE ATTEINTE - C'EST LE PROBLÈME !\n";
    }
} else {
    echo "\n❌ Action INSCRIPTION NON TROUVÉE\n";
}

// 3. Compter les récompenses totales
$totalRewards = Recompense::count();
echo "\nTotal récompenses dans la base : {$totalRewards}\n";

if ($totalRewards == 0) {
    echo "❌ BASE VIDE - C'est anormal en production !\n";
    echo "   Les nouveaux inscrits devraient avoir des récompenses.\n";
}
