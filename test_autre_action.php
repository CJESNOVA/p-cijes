<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Action;

echo "=== TEST AUTRE ACTION ===\n\n";

$membre = Membre::first();
echo "Membre : {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n\n";

// Chercher une action sans limite ou avec limite élevée
$actions = Action::where('etat', 1)
    ->where('limite', '>', 10)
    ->orWhere('limite', null)
    ->orderBy('limite', 'desc')
    ->limit(5)
    ->get();

echo "Actions disponibles avec limite élevée :\n";
foreach ($actions as $action) {
    $existingCount = \App\Models\Recompense::where('membre_id', $membre->id)
        ->where('action_id', $action->id)
        ->count();
    
    echo "- {$action->code}: {$action->titre} (limite: {$action->limite}, déjà: {$existingCount})\n";
}

// Prendre la première action disponible
$testAction = $actions->first();
if (!$testAction) {
    echo "❌ Aucune action disponible pour le test\n";
    exit;
}

echo "\nTest avec : {$testAction->code} - {$testAction->titre}\n";

try {
    $recompenseService = new RecompenseService();
    $result = $recompenseService->attribuerRecompense($testAction->code, $membre);
    
    if ($result) {
        echo "✅ Récompense attribuée avec succès !\n";
        echo "ID : {$result->id}\n";
        echo "Valeur : {$result->valeur} points\n";
        echo "Commentaire : {$result->commentaire}\n";
        
        // Vérifier l'alerte créée
        $alerte = \App\Models\Alerte::where('recompense_id', $result->id)->first();
        if ($alerte) {
            echo "✅ Alerte créée : {$alerte->titre}\n";
        } else {
            echo "❌ Alerte NON créée\n";
        }
        
    } else {
        echo "❌ Échec de l'attribution\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST ===\n";
