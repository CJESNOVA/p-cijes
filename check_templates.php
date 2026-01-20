<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$templatesPME = App\Models\Plantemplate::whereHas('diagnosticmodule', function($q) { 
    $q->where('diagnosticmoduletype_id', 1); 
})->count();

$templatesEntreprise = App\Models\Plantemplate::whereHas('diagnosticmodule', function($q) { 
    $q->where('diagnosticmoduletype_id', 2); 
})->count();

echo "Templates PME: $templatesPME\n";
echo "Templates Entreprise: $templatesEntreprise\n";

// Afficher quelques templates PME pour exemple
$templatesPMEList = App\Models\Plantemplate::whereHas('diagnosticmodule', function($q) { 
    $q->where('diagnosticmoduletype_id', 1); 
})->limit(3)->get(['id', 'objectif', 'diagnosticmodule_id']);

echo "\nExemples templates PME:\n";
foreach ($templatesPMEList as $template) {
    echo "ID: {$template->id} | Module: {$template->diagnosticmodule_id} | Objectif: {$template->objectif}\n";
}
