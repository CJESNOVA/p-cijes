<?php

echo "=== VÉRIFICATION DES AUTRES ENVOIS D'EMAILS ===\n\n";

echo "🔍 ANALYSE DES FICHIERS RESTANTS :\n";
echo str_repeat("=", 60) . "\n\n";

// Vérifier les contrôleurs
$controllersPath = __DIR__ . '/app/Http/Controllers';
$controllers = glob($controllersPath . '/*.php');

echo "📁 CONTRÔLEURS TROUVÉS :\n";
foreach ($controllers as $controller) {
    $controllerName = basename($controller, '.php');
    echo "- {$controllerName}\n";
}
echo "\n";

// Chercher tous les envois d'email dans les contrôleurs
echo "🔍 RECHERCHE D'ENVOIS D'EMAILS DANS LES CONTRÔLEURS :\n";
echo str_repeat("-", 60) . "\n";

$emailMethods = ['Mail::', '->notify(', 'Notification::'];
$foundEmails = [];

foreach ($controllers as $controller) {
    $content = file_get_contents($controller);
    $controllerName = basename($controller, '.php');
    
    foreach ($emailMethods as $method) {
        if (strpos($content, $method) !== false) {
            $foundEmails[] = $controllerName;
            echo "✅ {$controllerName} contient : {$method}\n";
            
            // Trouver les lignes spécifiques
            $lines = file($controller);
            foreach ($lines as $lineNum => $line) {
                if (strpos($line, $method) !== false) {
                    echo "   Ligne " . ($lineNum + 1) . ": " . trim($line) . "\n";
                }
            }
            echo "\n";
            break;
        }
    }
}

if (empty($foundEmails)) {
    echo "✅ Aucun envoi d'email trouvé dans les autres contrôleurs\n\n";
}

// Vérifier les services
echo "🔁 VÉRIFICATION DES SERVICES :\n";
echo str_repeat("-", 60) . "\n";

$servicesPath = __DIR__ . '/app/Services';
$services = glob($servicesPath . '/*.php');

foreach ($services as $service) {
    $serviceName = basename($service, '.php');
    if ($serviceName === 'RecompenseService') {
        echo "✅ {$serviceName} - DÉJÀ MODIFIÉ\n";
        continue;
    }
    
    $content = file_get_contents($service);
    foreach ($emailMethods as $method) {
        if (strpos($content, $method) !== false) {
            echo "⚠️  {$serviceName} contient : {$method}\n";
            
            $lines = file($service);
            foreach ($lines as $lineNum => $line) {
                if (strpos($line, $method) !== false) {
                    echo "   Ligne " . ($lineNum + 1) . ": " . trim($line) . "\n";
                }
            }
            echo "\n";
            break;
        }
    }
}

// Vérifier les modèles
echo "📋 VÉRIFICATION DES MODÈLES :\n";
echo str_repeat("-", 60) . "\n";

$modelsPath = __DIR__ . '/app/Models';
$models = glob($modelsPath . '/*.php');

foreach ($models as $model) {
    $modelName = basename($model, '.php');
    $content = file_get_contents($model);
    
    foreach ($emailMethods as $method) {
        if (strpos($content, $method) !== false) {
            echo "⚠️  {$modelName} contient : {$method}\n";
            
            $lines = file($model);
            foreach ($lines as $lineNum => $line) {
                if (strpos($line, $method) !== false) {
                    echo "   Ligne " . ($lineNum + 1) . ": " . trim($line) . "\n";
                }
            }
            echo "\n";
            break;
        }
    }
}

echo str_repeat("=", 60) . "\n";
echo "📊 RÉSUMÉ :\n\n";

echo "✅ DÉJÀ TRAITÉS :\n";
echo "- RecompenseService (modifié)\n";
echo "- AuthController (modifié)\n";
echo "- Routes web.php (déjà protégé)\n\n";

echo "🔍 À VÉRIFIER :\n";
echo "- MailTestController (tests seulement)\n";
echo "- Autres contrôleurs/services/modèles\n\n";

echo "🎯 PROCHAINE ÉTAPE :\n";
echo "Voulez-vous que je vérifie spécifiquement les fichiers trouvés ?\n";
