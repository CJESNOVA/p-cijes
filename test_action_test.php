<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Action;
use Illuminate\Support\Facades\DB;

echo "=== CRÉATION ACTION TEST ===\n\n";

try {
    // Créer une action de test si elle n'existe pas
    $testAction = Action::where('code', 'TEST_RECOMPENSE')->first();
    
    if (!$testAction) {
        $testAction = Action::create([
            'code' => 'TEST_RECOMPENSE',
            'titre' => 'Test de récompense',
            'point' => 777,
            'limite' => 999,
            'seuil' => null,
            'ressourcetype_id' => 2, // CORIS
            'etat' => 1
        ]);
        echo "✅ Action de test créée : {$testAction->titre}\n";
    } else {
        echo "ℹ️  Action de test existe déjà : {$testAction->titre}\n";
    }
    
    // Tester l'attribution
    $membre = Membre::first();
    echo "Membre : {$membre->prenom} {$membre->nom}\n";
    
    $recompenseService = new RecompenseService();
    $result = $recompenseService->attribuerRecompense('TEST_RECOMPENSE', $membre);
    
    if ($result) {
        echo "✅ Récompense de test attribuée !\n";
        echo "ID : {$result->id}\n";
        echo "Valeur : {$result->valeur} points\n";
        
        // Vérifier l'alerte
        $alerte = \App\Models\Alerte::where('recompense_id', $result->id)->first();
        if ($alerte) {
            echo "✅ Alerte : {$alerte->titre}\n";
        }
        
        // Vérifier le compte ressource
        $compte = \App\Models\Ressourcecompte::where('membre_id', $membre->id)
            ->where('ressourcetype_id', 2)
            ->first();
        if ($compte) {
            echo "✅ Solde CORIS : {$compte->solde}\n";
        }
        
    } else {
        echo "❌ Échec attribution\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN ===\n";
