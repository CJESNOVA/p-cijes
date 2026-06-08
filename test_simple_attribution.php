<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;

echo "=== TEST SIMPLE ATTRIBUTION ===\n\n";

try {
    // Récupérer le membre 18
    $membre = Membre::find(18);
    if (!$membre) {
        echo "❌ Membre 18 non trouvé\n";
        exit;
    }
    
    echo "✅ Membre trouvé : {$membre->prenom} {$membre->nom}\n";
    
    // Créer le service
    $recompenseService = new RecompenseService();
    echo "✅ Service créé\n";
    
    // Tenter l'attribution avec try-catch très large
    echo "🚀 Tentative d'attribution...\n";
    
    $result = $recompenseService->attribuerRecompense('CONNEXION_50', $membre);
    
    echo "📊 Résultat :\n";
    if ($result) {
        echo "✅ Succès ! ID: {$result->id}\n";
    } else {
        echo "❌ Échec (retourne false)\n";
    }
    
} catch (\Throwable $e) {
    echo "❌ EXCEPTION CAPTURÉE :\n";
    echo "Message : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace :\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== VÉRIFICATION BDD ===\n";

// Vérifier si la récompense a été créée malgré l'erreur
$recompenses = \App\Models\Recompense::where('membre_id', 18)
    ->where('action_id', 3) // CONNEXION_50
    ->get();

echo "Récompenses du membre 18 pour CONNEXION_50 : " . $recompenses->count() . "\n";
foreach ($recompenses as $r) {
    echo "- ID {$r->id}: {$r->valeur} points ({$r->created_at})\n";
}
