<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Action;

echo "=== TEST SYSTÈME RÉCOMPENSE + EMAIL ===\n\n";

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
$membre = Membre::find(18);
if (!$membre) {
    echo "❌ Aucun membre trouvé pour le test\n";
    exit;
}

echo "\nMembre de test : {$membre->prenom} {$membre->nom} ({$membre->email})\n\n";

// 4. Tester l'attribution de récompense
$recompenseService = new RecompenseService();
echo "Test d'attribution de récompense...\n";

$result = $recompenseService->attribuerRecompense(
    'INSCRIPTION', 
    $membre
);

if ($result) {
    echo "✅ Récompense attribuée avec succès !\n";
    echo "ID Récompense : {$result->id}\n";
    echo "Valeur : {$result->valeur} points\n";
    echo "Commentaire : {$result->commentaire}\n";
} else {
    echo "❌ Échec de l'attribution de récompense\n";
}

echo "\n=== FIN TEST ===\n";
