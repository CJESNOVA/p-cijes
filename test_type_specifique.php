<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test avec ressource_type_id spécifique ===\n\n";

use App\Http\Controllers\ActionPaiementController;
use App\Models\Membre;

// Simuler une action avec ressource_type_id = 4
$actionDataSpecifique = [
    'action' => (object)['id' => 999, 'code' => 'TEST_SPECIFIQUE'],
    'caracteristiques' => [
        'type_calcul' => 'fixe',
        'valeur_base' => 1000,
        'ressource_type_id' => 4, // Type spécifique
        'est_actif' => 1
    ],
    'strategie_ressource' => [
        'type' => 'automatique',
        'description' => 'Recherche automatique par ordre de priorité 2-3-4-1'
    ],
    'montant_retrait' => 1000
];

$membre = Membre::find(1);
$entrepriseIds = [1];

echo "Action TEST_SPECIFIQUE:\n";
echo "- ressource_type_id: " . $actionDataSpecifique['caracteristiques']['ressource_type_id'] . "\n";
echo "- montant_retrait: " . $actionDataSpecifique['montant_retrait'] . "\n";
echo "- strategie: " . $actionDataSpecifique['strategie_ressource']['type'] . "\n\n";

// Tester la recherche
$actionController = new ActionPaiementController();
$compte = $actionController->trouverCompteParPriorite(
    $membre, 
    $entrepriseIds, 
    $actionDataSpecifique['montant_retrait'],
    $actionDataSpecifique
);

if ($compte) {
    echo "Compte trouvé:\n";
    echo "- Type: " . $compte->ressourcetype_id . "\n";
    echo "- ID: " . $compte->id . "\n";
    echo "- Solde: " . $compte->solde . "\n";
    
    if ($actionDataSpecifique['caracteristiques']['ressource_type_id'] == 0) {
        echo "\n>>> COMPATIBILITÉ IGNORÉE (type = 0) <<<\n";
    } else {
        echo "\n>>> COMPATIBILITÉ VÉRIFIÉE (type = " . $actionDataSpecifique['caracteristiques']['ressource_type_id'] . ") <<<\n";
        echo ">>> SEUL LES COMPTES DE TYPE " . $actionDataSpecifique['caracteristiques']['ressource_type_id'] . " COMPATIBLES SERONT UTILISÉS <<<\n";
    }
} else {
    echo "Aucun compte compatible trouvé\n";
}
