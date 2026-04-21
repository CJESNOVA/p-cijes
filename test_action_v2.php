<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test TEST_CLASSIFICATION_V2 avec compatibilité ignorée ===\n\n";

use App\Http\Controllers\ActionPaiementController;
use App\Models\Membre;

// Récupérer le membre
$membre = Membre::find(1);
$entrepriseIds = [1]; // L'utilisateur a une entreprise

$actionController = new ActionPaiementController();

// 1. Récupérer les données de l'action
$actionData = $actionController->getActionPourPaiement('TEST_CLASSIFICATION_V2');

echo "Action TEST_CLASSIFICATION_V2:\n";
echo "- Structure complète:\n";
print_r($actionData);
echo "\n";

if (isset($actionData['ressourcetype_id'])) {
    echo "- resourcetype_id: " . $actionData['ressourcetype_id'] . "\n";
} else {
    echo "- resourcetype_id: NON DÉFINI (utilise 0 par défaut)\n";
}
echo "- montant_retrait: " . $actionData['montant_retrait'] . "\n";
echo "- strategie: " . $actionData['strategie_ressource']['type'] . "\n\n";

// 2. Trouver le compte
$montantRequis = $actionData['montant_retrait'];
$compte = $actionController->trouverComptePourAction(
    'TEST_CLASSIFICATION_V2', 
    $membre, 
    $entrepriseIds, 
    $montantRequis
);

if ($compte) {
    echo "Compte trouvé:\n";
    echo "- Type: " . $compte->ressourcetype_id . "\n";
    echo "- ID: " . $compte->id . "\n";
    echo "- Solde: " . $compte->solde . "\n";
    echo "- Entreprise: " . ($compte->entreprise_id ?? 'NULL') . "\n";
    
    if ($actionData['caracteristiques']['ressource_type_id'] == 0) {
        echo "\n>>> COMPATIBILITÉ IGNORÉE (ressource_type_id = 0) <<<\n";
    } else {
        echo "\n>>> COMPATIBILITÉ VÉRIFIÉE (ressource_type_id != 0) <<<\n";
    }
} else {
    echo "Aucun compte trouvé\n";
}
