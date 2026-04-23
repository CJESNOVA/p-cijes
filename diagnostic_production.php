<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSTIC COMPLET PRODUCTION ===\n\n";

// 1. Paramètres .env
echo "📋 PARAMÈTRES .ENV\n";
echo str_repeat("=", 50) . "\n";
echo "DB_CONNECTION: " . env('DB_CONNECTION', 'non défini') . "\n";
echo "DB_HOST: " . env('DB_HOST', 'non défini') . "\n";
echo "DB_PORT: " . env('DB_PORT', 'non défini') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE', 'non défini') . "\n";
echo "DB_USERNAME: " . env('DB_USERNAME', 'non défini') . "\n";
echo "DB_PASSWORD: " . (env('DB_PASSWORD') ? '*** défini ***' : 'non défini') . "\n";

echo "\n📧 PARAMÈTRES MAIL\n";
echo str_repeat("-", 30) . "\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER', 'non défini') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', 'non défini') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT', 'non défini') . "\n";
echo "MAIL_USERNAME: " . (env('MAIL_USERNAME') ? 'défini' : 'non défini') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'non défini') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME', 'non défini') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION', 'non défini') . "\n";

// 2. Test de connexion
echo "\n🔌 TEST DE CONNEXION\n";
echo str_repeat("=", 50) . "\n";

try {
    // Test simple
    $pdo = DB::connection()->getPdo();
    echo "✅ Connexion à la base de données réussie\n";
    echo "   Driver: " . DB::connection()->getDriverName() . "\n";
    echo "   Version: " . DB::connection()->select('SELECT VERSION() as version')[0]->version . "\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Tables users
echo "\n👥 TABLE USERS\n";
echo str_repeat("=", 50) . "\n";

try {
    $users = DB::table('users')->get();
    echo "Nombre d'utilisateurs: " . $users->count() . "\n\n";
    
    if ($users->count() > 0) {
        echo "Liste des utilisateurs (5 premiers):\n";
        foreach ($users->take(5) as $user) {
            echo "ID: {$user->id} | Nom: {$user->name} | Email: {$user->email} | Créé: {$user->created_at}\n";
        }
        
        if ($users->count() > 5) {
            echo "... et " . ($users->count() - 5) . " autres\n";
        }
    } else {
        echo "❌ Aucun utilisateur trouvé\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur lecture users: " . $e->getMessage() . "\n";
}

// 4. Tables membres
echo "\n👤 TABLE MEMBRES\n";
echo str_repeat("=", 50) . "\n";

try {
    $membres = DB::table('membres')->get();
    echo "Nombre de membres: " . $membres->count() . "\n\n";
    
    if ($membres->count() > 0) {
        echo "Liste des membres (tous):\n";
        foreach ($membres as $membre) {
            echo "ID: {$membre->id} | Nom: {$membre->nom} {$membre->prenom} | Email: {$membre->email} | User ID: " . ($membre->user_id ?? 'null') . " | Créé: {$membre->created_at}\n";
        }
        
        // Vérifier spécifiquement le membre 18
        $membre18 = DB::table('membres')->where('id', 18)->first();
        if ($membre18) {
            echo "\n✅ Membre 18 trouvé: {$membre18->prenom} {$membre18->nom} ({$membre18->email})\n";
        } else {
            echo "\n❌ Membre 18 NON TROUVÉ\n";
        }
    } else {
        echo "❌ Aucun membre trouvé\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur lecture membres: " . $e->getMessage() . "\n";
}

// 5. Tables recompenses
echo "\n🎁 TABLE RECOMPENSES\n";
echo str_repeat("=", 50) . "\n";

try {
    $recompenses = DB::table('recompenses')->get();
    echo "Nombre de récompenses: " . $recompenses->count() . "\n\n";
    
    if ($recompenses->count() > 0) {
        echo "Liste des récompenses (10 premières):\n";
        
        foreach ($recompenses->take(10) as $rec) {
            // Récupérer le nom du membre et de l'action
            $membre = DB::table('membres')->where('id', $rec->membre_id)->first();
            $action = DB::table('actions')->where('id', $rec->action_id)->first();
            
            $membreNom = $membre ? "{$membre->prenom} {$membre->nom}" : "Membre {$rec->membre_id}";
            $actionNom = $action ? $action->titre : "Action {$rec->action_id}";
            
            echo "ID: {$rec->id} | {$membreNom} | {$actionNom} | {$rec->valeur} pts | {$rec->dateattribution}\n";
        }
        
        if ($recompenses->count() > 10) {
            echo "... et " . ($recompenses->count() - 10) . " autres\n";
        }
        
        // Récompenses du membre 18
        $recompenses18 = DB::table('recompenses')->where('membre_id', 18)->get();
        echo "\nRécompenses du membre 18: " . $recompenses18->count() . "\n";
        foreach ($recompenses18 as $rec) {
            $action = DB::table('actions')->where('id', $rec->action_id)->first();
            $actionNom = $action ? $action->titre : "Action {$rec->action_id}";
            echo "  - {$actionNom}: {$rec->valeur} points ({$rec->dateattribution})\n";
        }
        
    } else {
        echo "❌ Aucune récompense trouvée\n";
        echo "🚨 C'EST ANORMAL EN PRODUCTION ! Les membres devraient avoir des récompenses.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur lecture recompenses: " . $e->getMessage() . "\n";
}

// 6. Actions et limites
echo "\n⚙️ TABLE ACTIONS\n";
echo str_repeat("=", 50) . "\n";

try {
    $actions = DB::table('actions')->where('etat', 1)->orderBy('code')->get();
    echo "Actions actives: " . $actions->count() . "\n\n";
    
    foreach ($actions as $action) {
        $used = DB::table('recompenses')->where('action_id', $action->id)->count();
        $status = ($action->limite && $used >= $action->limite) ? "❌ LIMITE ATTEINTE" : "✅ DISPONIBLE";
        
        echo "Code: {$action->code} | {$action->titre} | Limite: {$action->limite} | Utilisé: {$used} | $status\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur lecture actions: " . $e->getMessage() . "\n";
}

// 7. Alertes
echo "\n🔔 TABLE ALERTES\n";
echo str_repeat("=", 50) . "\n";

try {
    $alertes = DB::table('alertes')->get();
    echo "Nombre d'alertes: " . $alertes->count() . "\n\n";
    
    if ($alertes->count() > 0) {
        echo "Alertes récentes (5 premières):\n";
        foreach ($alertes->take(5) as $alerte) {
            $membre = DB::table('membres')->where('id', $alerte->membre_id)->first();
            $membreNom = $membre ? "{$membre->prenom} {$membre->nom}" : "Membre {$alerte->membre_id}";
            
            echo "ID: {$alerte->id} | {$membreNom} | {$alerte->titre} | Lu: " . ($alerte->lu ? 'Oui' : 'Non') . " | {$alerte->datealerte}\n";
        }
    } else {
        echo "❌ Aucune alerte trouvée\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur lecture alertes: " . $e->getMessage() . "\n";
}

// 8. Résumé et diagnostic
echo "\n📊 RÉSUMÉ ET DIAGNOSTIC\n";
echo str_repeat("=", 50) . "\n";

$usersCount = DB::table('users')->count();
$membresCount = DB::table('membres')->count();
$recompensesCount = DB::table('recompenses')->count();
$alertesCount = DB::table('alertes')->count();

echo "Users: $usersCount | Membres: $membresCount | Récompenses: $recompensesCount | Alertes: $alertesCount\n\n";

echo "🔍 DIAGNOSTIC:\n";

if ($membresCount == 0) {
    echo "❌ Aucun membre - Problème d'inscription\n";
} elseif ($recompensesCount == 0) {
    echo "❌ Aucune récompense - Le système n'a jamais fonctionné\n";
    echo "   💡 Vérifiez que le code d'attribution s'exécute lors des inscriptions\n";
} else {
    echo "✅ Le système a fonctionné - $recompensesCount récompenses attribuées\n";
}

if ($membresCount > 0 && $recompensesCount == 0) {
    echo "\n🚨 ACTION RECOMMANDÉE:\n";
    echo "1. Augmentez les limites des actions INSCRIPTION et PROFIL_COMPLET\n";
    echo "2. Exécutez un script de réparation pour les membres existants\n";
    echo "3. Testez l'attribution avec un membre existant\n";
}

echo "\n✅ Diagnostic terminé !\n";
