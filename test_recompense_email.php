<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

echo "=== TEST SYSTÈME RÉCOMPENSE + EMAIL (MODE DEBUG) ===\n\n";

// Activer les logs d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Activer le logging de Laravel pour les requêtes DB
DB::enableQueryLog();
DB::listen(function ($query) {
    echo "🔍 SQL: " . $query->sql . " | Bindings: " . json_encode($query->bindings) . "\n";
});

// 1. Vérifier la configuration mail
echo "Configuration Mail :\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER', 'non défini') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', 'non défini') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT', 'non défini') . "\n";
echo "MAIL_USERNAME: " . (env('MAIL_USERNAME') ? 'défini' : 'non défini') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'non défini') . "\n\n";

// 2. Vérifier les actions disponibles
$actions = Action::where('etat', 1)->get();
echo "Actions disponibles :\n";
foreach ($actions as $action) {
    echo "- {$action->code}: {$action->titre} ({$action->point} points)\n";
}

// 3. Tester avec un membre
$membre = Membre::find(1);
if (!$membre) {
    echo "❌ Aucun membre trouvé pour le test\n";
    exit;
}
    
echo "\nMembre de test : {$membre->prenom} {$membre->nom} ({$membre->email})\n\n";

// 4. Tester l'attribution de récompense
$recompenseService = new RecompenseService();
echo "Test d'attribution de récompense...\n\n";

echo "📋 INFORMATIONS AVANT ATTRIBUTION :\n";
echo "Membre : {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n";
echo "Email : {$membre->email}\n";
echo "Entreprise ID : " . ($membre->entreprise_id ?? 'null') . "\n";

$action = Action::where('code', 'CONNEXION_50')->first();
if ($action) {
    echo "Action : {$action->titre} (Code: {$action->code})\n";
    echo "Points : {$action->point}\n";
    echo "Limite : {$action->limite}\n";
    
    // Vérifier les récompenses existantes
    $existingCount = \App\Models\Recompense::where('membre_id', $membre->id)
        ->where('action_id', $action->id)
        ->count();
    $globalCount = \App\Models\Recompense::where('action_id', $action->id)->count();
    
    echo "Récompenses existantes pour ce membre : {$existingCount}\n";
    echo "Récompenses globales pour cette action : {$globalCount}\n";
    
    if ($action->limite && $globalCount >= $action->limite) {
        echo "❌ LIMITE GLOBALE ATTEINTE !\n";
    } else {
        echo "✅ Attribution possible\n";
    }
} else {
    echo "❌ Action CONNEXION_50 non trouvée\n";
}

echo "\n🚀 DÉBUT DE L'ATTRIBUTION :\n";
echo str_repeat("-", 50) . "\n";

try {
    $result = $recompenseService->attribuerRecompense(
        'CONNEXION_50', 
        $membre
    );

    echo str_repeat("-", 50) . "\n";
    echo "📊 RÉSULTAT DE L'ATTRIBUTION :\n";
    
    if ($result) {
        echo "✅ Récompense attribuée avec succès !\n";
        echo "ID Récompense : {$result->id}\n";
        echo "Valeur : {$result->valeur} points\n";
        echo "Commentaire : {$result->commentaire}\n";
        echo "Date : {$result->dateattribution}\n";
        
        // Vérifier l'alerte créée
        $alerte = \App\Models\Alerte::where('recompense_id', $result->id)->first();
        if ($alerte) {
            echo "✅ Alerte créée : {$alerte->titre}\n";
            echo "   ID Alerte : {$alerte->id}\n";
            echo "   Contenu : {$alerte->contenu}\n";
        } else {
            echo "❌ Alerte NON créée\n";
        }
        
        // Vérifier le compte ressource
        $compte = \App\Models\Ressourcecompte::where('membre_id', $membre->id)
            ->where('ressourcetype_id', $action->ressourcetype_id)
            ->first();
        if ($compte) {
            echo "✅ Compte ressource mis à jour\n";
            echo "   Type ID : {$compte->ressourcetype_id}\n";
            echo "   Nouveau solde : {$compte->solde}\n";
        }
        
    } else {
        echo "❌ Échec de l'attribution de récompense\n";
        echo "   Possible cause : limite atteinte ou erreur silencieuse\n";
    }
    
} catch (\Exception $e) {
    echo str_repeat("-", 50) . "\n";
    echo "❌ EXCEPTION CAPTURÉE :\n";
    echo "Message : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace :\n" . $e->getTraceAsString() . "\n";
}

echo "\n📋 REQUÊTES SQL EXÉCUTÉES :\n";
$queries = DB::getQueryLog();
foreach ($queries as $query) {
    echo "SQL: " . $query['query'] . "\n";
    echo "Bindings: " . json_encode($query['bindings']) . "\n";
    echo "Time: " . $query['time'] . "ms\n\n";
}

echo "\n=== FIN TEST ===\n";
