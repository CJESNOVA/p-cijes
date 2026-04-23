<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CONFIGURATION ACTUELLE APRÈS CACHE CLEAR ===\n\n";

echo "Base de données actuelle :\n";
echo "DB_HOST: " . env('DB_HOST') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "DB_USERNAME: " . env('DB_USERNAME') . "\n";
echo "DB_PORT: " . env('DB_PORT') . "\n";

echo "\nTest de connexion...\n";
try {
    $count = \App\Models\Membre::count();
    echo "✅ Connexion réussie !\n";
    echo "Nombre de membres : $count\n";
    
    // Vérifier le membre 18
    $membre18 = \App\Models\Membre::find(18);
    if ($membre18) {
        echo "✅ Membre 18 trouvé : {$membre18->prenom} {$membre18->nom}\n";
    } else {
        echo "❌ Membre 18 non trouvé\n";
    }
    
    // Vérifier les récompenses
    $rewards = \App\Models\Recompense::count();
    echo "Nombre de récompenses : $rewards\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'getaddrinfo') !== false) {
        echo "\n💡 L'hôte n'est pas accessible. Solutions :\n";
        echo "1. Vérifiez que le nom d'hôte est correct\n";
        echo "2. Testez avec l'IP directe du serveur\n";
        echo "3. Vérifiez votre connexion VPN/réseau\n";
        echo "4. Assurez-vous que le serveur MySQL accepte les connexions externes\n";
    }
}

echo "\n=== CONSEILS ===\n";
echo "1. Si l'hôte est incorrect, modifiez .env avec les bonnes informations\n";
echo "2. Après modification du .env, relancez : php artisan config:clear\n";
echo "3. Testez à nouveau avec ce script\n";
