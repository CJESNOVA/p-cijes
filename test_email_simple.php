<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;

echo "=== TEST EMAIL SIMPLE ===\n\n";

// Récupérer un membre
$membre = Membre::first();
if (!$membre) {
    echo "❌ Aucun membre trouvé\n";
    exit;
}

echo "Membre : {$membre->prenom} {$membre->nom} ({$membre->email})\n\n";

// Test avec une action simple
$recompenseService = new RecompenseService();

echo "Test attribution récompense...\n";
$result = $recompenseService->attribuerRecompense(
    'PROFIL_COMPLET', 
    $membre
);

if ($result) {
    echo "✅ Succès ! ID: {$result->id}, Valeur: {$result->valeur}\n";
} else {
    echo "❌ Échec\n";
}

echo "\nVérification des logs email...\n";

// Vérifier le contenu du log mail
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $recentEntries = substr($logContent, -2000); // Derniers 2000 caractères
    
    if (strpos($recentEntries, 'to: ' . $membre->email) !== false) {
        echo "✅ Email trouvé dans les logs pour {$membre->email}\n";
    } else {
        echo "❌ Email non trouvé dans les logs\n";
    }
    
    if (strpos($recentEntries, 'RecompenseNotification') !== false) {
        echo "✅ Notification de récompense traitée\n";
    }
}

echo "\n=== FIN TEST ===\n";
