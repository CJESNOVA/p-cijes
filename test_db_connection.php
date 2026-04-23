<?php

echo "=== TEST CONNEXION BASE PRODUCTION ===\n\n";

// Test avec différents hôtes possibles
$hosts = [
    'cijes-ecijesdb-aiw0dp',  // Nom d'hôte actuel
    'localhost',              // Local
    '127.0.0.1',             // IP locale
    // Ajoutez ici l'IP de votre serveur de production
    'VOTRE_IP_PRODUCTION',  // 🔧 REMPLACEZ CECI
];

$config = [
    'database' => 'ecijes',
    'username' => 'ptchabao',
    'password' => '', // Ajoutez le mot de passe si nécessaire
    'port' => '3306'
];

foreach ($hosts as $host) {
    echo "Test de connexion à : $host\n";
    
    try {
        $dsn = "mysql:host=$host;port={$config['port']};dbname={$config['database']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        echo "✅ Connexion réussie à $host\n";
        
        // Test simple
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM membres");
        $count = $stmt->fetch()['count'];
        echo "   Nombre de membres : $count\n";
        
        // Si ça marche, c'est le bon hôte !
        echo "\n🎯 BON HÔTE TROUVÉ : $host\n";
        echo "Modifiez votre .env avec :\n";
        echo "DB_HOST=$host\n";
        break;
        
    } catch (PDOException $e) {
        echo "❌ Erreur : " . $e->getMessage() . "\n";
    }
    
    echo str_repeat("-", 50) . "\n";
}

echo "\n💡 Si aucun ne fonctionne :\n";
echo "1. Vérifiez que vous avez bien l'IP du serveur de base de données\n";
echo "2. Assurez-vous que le serveur MySQL accepte les connexions externes\n";
echo "3. Vérifiez que vous avez un VPN ou accès réseau au serveur\n";
echo "4. Contactez votre administrateur système pour les bonnes informations\n";
