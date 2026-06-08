<?php

echo "=== BASCULEMENT ENVIRONNEMENT ===\n\n";

echo "Choisissez l'environnement :\n";
echo "1. Local (base de données locale)\n";
echo "2. Production (base de données de production)\n";
echo "3. Afficher la configuration actuelle\n\n";

// Lire le .env actuel
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    echo "Configuration .env actuelle :\n";
    
    if (preg_match('/DB_HOST=(.+)/', $envContent, $matches)) {
        echo "DB_HOST: " . trim($matches[1]) . "\n";
    }
    if (preg_match('/DB_DATABASE=(.+)/', $envContent, $matches)) {
        echo "DB_DATABASE: " . trim($matches[1]) . "\n";
    }
    if (preg_match('/DB_USERNAME=(.+)/', $envContent, $matches)) {
        echo "DB_USERNAME: " . trim($matches[1]) . "\n";
    }
} else {
    echo "Fichier .env non trouvé\n";
}

echo "\n" . str_repeat("-", 50) . "\n";
echo "Pour basculer vers la production :\n\n";

echo "1. MODIFIEZ MANUELLEMENT votre fichier .env :\n";
echo "   DB_HOST=votre-host-production\n";
echo "   DB_DATABASE=votre-base-production\n";
echo "   DB_USERNAME=votre-user-production\n";
echo "   DB_PASSWORD=votre-password-production\n\n";

echo "2. PUIS testez avec :\n";
echo "   php test_recompense_email.php\n\n";

echo "3. POUR revenir en local :\n";
echo "   DB_HOST=127.0.0.1\n";
echo "   DB_DATABASE=cjes\n";
echo "   DB_USERNAME=root\n";
echo "   DB_PASSWORD=\n\n";

echo "⚠️  ATTENTION :\n";
echo "- Sauvegardez votre .env avant de modifier\n";
echo "- Ne commitez jamais le .env de production\n";
echo "- Testez d'abord sur une copie de la base\n\n";

echo "📝 Script de sauvegarde automatique :\n";
echo "cp .env .env.backup\n";
echo "# Modifiez .env pour la production\n";
echo "# Testez\n";
echo "# cp .env.backup .env  # Pour revenir en local\n";
