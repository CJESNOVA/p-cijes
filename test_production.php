<?php

echo "=== TEST DIRECT BASE PRODUCTION ===\n\n";

// 🔧 MODIFIEZ CES INFORMATIONS AVEC VOS ACCÈS PRODUCTION
$prodConfig = [
    'host' => 'localhost',           // 🔄 Changez ceci
    'port' => '3306',               // 🔄 Changez si besoin
    'database' => 'cjes',           // 🔄 Changez ceci
    'username' => 'root',           // 🔄 Changez ceci
    'password' => '',               // 🔄 Changez ceci
];

echo "Configuration de production :\n";
echo "Host: {$prodConfig['host']}\n";
echo "Port: {$prodConfig['port']}\n";
echo "Database: {$prodConfig['database']}\n";
echo "Username: {$prodConfig['username']}\n";
echo "Password: " . ($prodConfig['password'] ? '***' : '(vide)') . "\n\n";

try {
    $dsn = "mysql:host={$prodConfig['host']};port={$prodConfig['port']};dbname={$prodConfig['database']}";
    $pdo = new PDO($dsn, $prodConfig['username'], $prodConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à la production réussie !\n\n";
    
    // 1. Vérifier les membres
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM membres");
    $totalMembres = $stmt->fetch()['count'];
    echo "📊 MEMBRES EN PRODUCTION :\n";
    echo "   Total : $totalMembres\n";
    
    // Afficher les 5 premiers membres
    $stmt = $pdo->query("SELECT id, nom, prenom, email, created_at FROM membres ORDER BY created_at DESC LIMIT 5");
    $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   Derniers membres :\n";
    foreach ($membres as $m) {
        echo "   - ID {$m['id']}: {$m['prenom']} {$m['nom']} ({$m['email']}) - {$m['created_at']}\n";
    }
    
    // 2. Vérifier le membre 18 spécifiquement
    echo "\n👤 VÉRIFICATION MEMBRE 18 :\n";
    $stmt = $pdo->prepare("SELECT id, nom, prenom, email, created_at FROM membres WHERE id = 18");
    $stmt->execute();
    $membre18 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($membre18) {
        echo "   ✅ Membre 18 trouvé : {$membre18['prenom']} {$membre18['nom']}\n";
        echo "   Email : {$membre18['email']}\n";
        echo "   Créé : {$membre18['created_at']}\n";
        
        // Vérifier ses récompenses
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recompenses WHERE membre_id = 18");
        $stmt->execute();
        $rewards18 = $stmt->fetch()['count'];
        echo "   Récompenses : $rewards18\n";
        
        if ($rewards18 == 0) {
            echo "   ❌ PROBLÈME : Le membre 18 n'a aucune récompense !\n";
        }
    } else {
        echo "   ❌ Membre 18 NON TROUVÉ en production\n";
    }
    
    // 3. Vérifier les récompenses
    echo "\n🎁 RÉCOMPENSES EN PRODUCTION :\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM recompenses");
    $totalRewards = $stmt->fetch()['count'];
    echo "   Total récompenses : $totalRewards\n";
    
    if ($totalRewards == 0) {
        echo "   ❌ PROBLÈME : Aucune récompense en production !\n";
        echo "   📝 Les nouveaux inscrits ne reçoivent rien...\n";
    }
    
    // 4. Vérifier les actions et leurs limites
    echo "\n⚙️ ACTIONS ET LIMITES :\n";
    $stmt = $pdo->query("SELECT code, titre, limite FROM actions WHERE etat = 1 ORDER BY code");
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($actions as $action) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recompenses r JOIN actions a ON r.action_id = a.id WHERE a.code = ?");
        $stmt->execute([$action['code']]);
        $used = $stmt->fetch()['count'];
        
        echo "   {$action['code']}: limite {$action['limite']}, utilisé $used fois";
        
        if ($action['limite'] && $used >= $action['limite']) {
            echo " ❌ LIMITE ATTEINTE";
        } else {
            echo " ✅ DISPONIBLE";
        }
        echo "\n";
    }
    
    // 5. Test d'attribution pour le membre 18
    if ($membre18) {
        echo "\n🧪 TEST D'ATTRIBUTION POUR MEMBRE 18 :\n";
        
        // Vérifier si INSCRIPTION est disponible
        $stmt = $pdo->prepare("SELECT id, limite FROM actions WHERE code = 'INSCRIPTION'");
        $stmt->execute();
        $inscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($inscription) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recompenses WHERE action_id = ? AND membre_id = ?");
            $stmt->execute([$inscription['id'], 18]);
            $memberUsed = $stmt->fetch()['count'];
            
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recompenses WHERE action_id = ?");
            $stmt->execute([$inscription['id']]);
            $totalUsed = $stmt->fetch()['count'];
            
            echo "   INSCRIPTION: limite {$inscription['limite']}, utilisé $totalUsed fois globalement, $memberUsed fois par ce membre\n";
            
            if ($inscription['limite'] && $totalUsed >= $inscription['limite']) {
                echo "   ❌ Impossible d'attribuer : limite globale atteinte\n";
                echo "   💡 Solution : Augmentez la limite dans la base de production\n";
            } else {
                echo "   ✅ Attribution possible pour ce membre\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion à la production :\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "💡 Solutions :\n";
    echo "   1. Vérifiez les informations de connexion\n";
    echo "   2. Assurez-vous que la base est accessible\n";
    echo "   3. Vérifiez les permissions de l'utilisateur\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 RÉSUMÉ :\n";
echo "1. Si le membre 18 existe mais n'a pas de récompenses → problème de code\n";
echo "2. Si les limites sont atteintes → augmentez les limites en production\n";
echo "3. Si aucune récompense n'existe → le système n'a jamais fonctionné\n";
echo "4. Comparez avec votre base locale pour comprendre les différences\n";
