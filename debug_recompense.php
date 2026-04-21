<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Action;
use Illuminate\Support\Facades\Log;

echo "=== DÉBOGAGE RECOMPENSE ===\n\n";

// Activer le logging d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Récupérer le membre
$membre = Membre::first();
if (!$membre) {
    echo "❌ Aucun membre trouvé\n";
    exit;
}

echo "Membre : {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n";
echo "Email : {$membre->email}\n";
echo "Entreprise ID : " . ($membre->entreprise_id ?? 'null') . "\n\n";

// Vérifier l'action INSCRIPTION
$action = Action::where('code', 'INSCRIPTION')->first();
if (!$action) {
    echo "❌ Action INSCRIPTION non trouvée\n";
    exit;
}

echo "Action : {$action->titre} ({$action->point} points)\n";
echo "Limite : {$action->limite}\n";
echo "Etat : {$action->etat}\n\n";

// Vérifier les récompenses existantes
$existingRewards = \App\Models\Recompense::where('membre_id', $membre->id)
    ->where('action_id', $action->id)
    ->count();

echo "Récompenses existantes pour cette action : {$existingRewards}\n";
echo "Limite autorisée : {$action->limite}\n\n";

if ($existingRewards >= $action->limite) {
    echo "❌ LIMITE ATTEINTE - C'est probablement le problème !\n";
    echo "Le membre a déjà {$existingRewards} récompenses, limite = {$action->limite}\n";
    echo "Solution : Augmenter la limite ou utiliser une autre action\n";
} else {
    echo "✅ Limite non atteinte, attribution possible\n";
    
    // Tenter l'attribution avec capture d'erreurs
    try {
        $recompenseService = new RecompenseService();
        
        echo "Tentative d'attribution...\n";
        $result = $recompenseService->attribuerRecompense('INSCRIPTION', $membre);
        
        if ($result) {
            echo "✅ Succès ! ID: {$result->id}, Valeur: {$result->valeur}\n";
        } else {
            echo "❌ Échec sans erreur apparente\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Exception capturée : " . $e->getMessage() . "\n";
        echo "Fichier : " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "Trace :\n" . $e->getTraceAsString() . "\n";
    }
}

// Vérifier les logs Laravel récents
echo "\n=== DERNIERS LOGS LARAVEL ===\n";
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $recentLogs = substr($logContent, -2000);
    
    if (strpos($recentLogs, 'ERROR') !== false) {
        echo "Erreurs trouvées dans les logs récents :\n";
        $lines = explode("\n", $recentLogs);
        foreach ($lines as $line) {
            if (strpos($line, 'ERROR') !== false) {
                echo $line . "\n";
            }
        }
    } else {
        echo "Aucune erreur dans les logs récents\n";
    }
}

echo "\n=== FIN DÉBOGAGE ===\n";
