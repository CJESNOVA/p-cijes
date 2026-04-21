<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\User;

echo "=== TEST JAPHET ===\n\n";

// Récupérer Japhet
$japhet = Membre::where('prenom', 'Japhet')->first();
if (!$japhet) {
    echo "Japhet non trouvé\n";
    exit;
}

echo "Membre : {$japhet->prenom} (ID: {$japhet->id})\n";
echo "Créé : {$japhet->created_at}\n";
echo "User ID : {$japhet->user_id}\n";

// Vérifier si l'utilisateur existe
$user = User::find($japhet->user_id);
if ($user) {
    echo "User : {$user->name} ({$user->email})\n";
} else {
    echo "User non trouvé !\n";
}

// Vérifier ses récompenses
$rewards = \App\Models\Recompense::where('membre_id', $japhet->id)->get();
echo "\nRécompenses existantes : {$rewards->count()}\n";
foreach ($rewards as $r) {
    echo "- {$r->action->code}: {$r->valeur} points ({$r->dateattribution})\n";
}

// Tenter d'attribuer INSCRIPTION
echo "\nTest attribution INSCRIPTION...\n";
try {
    $recompenseService = new RecompenseService();
    $result = $recompenseService->attribuerRecompense('INSCRIPTION', $japhet);
    
    if ($result) {
        echo "SUCCESS ! ID: {$result->id}\n";
    } else {
        echo "FAILED - probablement limite atteinte\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Tenter d'attribuer CONNEXION_50
echo "\nTest attribution CONNEXION_50...\n";
try {
    $result = $recompenseService->attribuerRecompense('CONNEXION_50', $japhet);
    
    if ($result) {
        echo "SUCCESS ! ID: {$result->id}\n";
    } else {
        echo "FAILED\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN ===\n";
