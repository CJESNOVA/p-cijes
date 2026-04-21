<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;

echo "=== TEST MEMBRES RÉCENTS ===\n\n";

// Récupérer les 3 membres les plus récents
$membres = Membre::orderBy('created_at', 'desc')->limit(3)->get();

foreach ($membres as $membre) {
    echo "=== MEMBRE ===\n";
    echo "ID: {$membre->id}\n";
    echo "Nom: {$membre->prenom} {$membre->nom}\n";
    echo "Email: {$membre->email}\n";
    echo "Créé: {$membre->created_at}\n";
    echo "User ID: {$membre->user_id}\n";
    
    // Vérifier ses récompenses
    $rewards = \App\Models\Recompense::where('membre_id', $membre->id)->count();
    echo "Récompenses: {$rewards}\n";
    
    // Tenter d'attribuer INSCRIPTION
    try {
        $recompenseService = new RecompenseService();
        $result = $recompenseService->attribuerRecompense('INSCRIPTION', $membre);
        
        if ($result) {
            echo "INSCRIPTION: SUCCESS (ID: {$result->id})\n";
        } else {
            echo "INSCRIPTION: FAILED (limite atteinte)\n";
        }
    } catch (\Exception $e) {
        echo "INSCRIPTION: ERROR - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== FIN ===\n";
