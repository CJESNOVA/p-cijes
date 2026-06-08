<?php

echo "=== VÉRIFICATION BASES LOCALE & PRODUCTION ===\n\n";

// Configuration locale
$localConfig = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'cjes',
    'username' => 'root',
    'password' => ''
];

// Configuration production (à adapter selon votre config)
$prodConfig = [
    'host' => 'votre-host-production.com',
    'port' => '3306',
    'database' => 'cjes_prod',
    'username' => 'user_prod',
    'password' => 'password_prod'
];

function checkDatabase($name, $config) {
    echo "\n=== BASE $name ===\n";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        
        echo "✅ Connexion réussie\n";
        
        // Vérifier les membres
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM membres");
        $membres = $stmt->fetch()['count'];
        echo "Membres : $membres\n";
        
        // Vérifier les récompenses
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM recompenses");
        $recompenses = $stmt->fetch()['count'];
        echo "Récompenses : $recompenses\n";
        
        // Vérifier le membre 18
        $stmt = $pdo->query("SELECT id, nom, prenom, email FROM membres WHERE id = 18");
        $membre18 = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($membre18) {
            echo "Membre 18 : {$membre18['prenom']} {$membre18['nom']} ({$membre18['email']})\n";
            
            // Vérifier ses récompenses
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recompenses WHERE membre_id = 18");
            $stmt->execute();
            $rewards18 = $stmt->fetch()['count'];
            echo "Récompenses du membre 18 : $rewards18\n";
        } else {
            echo "❌ Membre 18 non trouvé\n";
        }
        
        // Vérifier les actions INSCRIPTION
        $stmt = $pdo->query("SELECT limite FROM actions WHERE code = 'INSCRIPTION'");
        $action = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($action) {
            echo "Limite INSCRIPTION : {$action['limite']}\n";
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM recompenses r JOIN actions a ON r.action_id = a.id WHERE a.code = 'INSCRIPTION'");
            $used = $stmt->fetch()['count'];
            echo "INSCRIPTION utilisée : $used fois\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Erreur de connexion : " . $e->getMessage() . "\n";
    }
}

// Vérifier la base locale
checkDatabase("LOCALE", $localConfig);

echo "\n" . str_repeat("=", 50) . "\n";
echo "POUR VÉRIFIER LA PRODUCTION :\n";
echo "1. Modifiez les configurations ci-dessus\n";
echo "2. Assurez-vous d'avoir accès à la base de production\n";
echo "3. Relancez ce script\n\n";

echo "OU utilisez la configuration Laravel existante :\n";

// Vérifier avec la config Laravel actuelle
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "\n=== CONFIGURATION LARAVEL ACTUELLE ===\n";
    echo "DB_HOST: " . env('DB_HOST') . "\n";
    echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
    echo "DB_USERNAME: " . env('DB_USERNAME') . "\n";
    
    $count = \App\Models\Membre::count();
    echo "Membres (Laravel) : $count\n";
    
    $count = \App\Models\Recompense::count();
    echo "Récompenses (Laravel) : $count\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur Laravel : " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMANDATIONS ===\n";
echo "1. Si vous êtes en production, changez .env pour pointer sur la base de production\n";
echo "2. Sinon, créez un script de test qui se connecte directement à la production\n";
echo "3. Vérifiez que les actions INSCRIPTION ont des limites suffisantes en production\n";
