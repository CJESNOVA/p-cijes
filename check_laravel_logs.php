<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VÉRIFICATION LOGS LARAVEL ===\n\n";

$logFile = storage_path('logs/laravel.log');

if (file_exists($logFile)) {
    echo "Fichier de log : $logFile\n";
    echo "Taille : " . filesize($logFile) . " octets\n\n";
    
    // Lire les dernières lignes du log
    $lines = file($logFile);
    $recentLines = array_slice($lines, -50); // 50 dernières lignes
    
    echo "Dernières entrées de log :\n";
    echo str_repeat("=", 80) . "\n";
    
    foreach ($recentLines as $line) {
        echo $line;
    }
    
} else {
    echo "❌ Fichier de log non trouvé : $logFile\n";
    
    // Vérifier d'autres fichiers de log possibles
    $logDir = storage_path('logs');
    if (is_dir($logDir)) {
        echo "\nFichiers dans le répertoire de logs :\n";
        $files = scandir($logDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "- $file\n";
            }
        }
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "💡 Si vous voyez des erreurs dans les logs, c'est là que le problème se trouve !\n";
