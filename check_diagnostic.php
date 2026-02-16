<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$diagnostic = App\Models\Diagnostic::find(17);
$scores = $diagnostic->diagnosticmodulescores;

echo "=== Vérification Diagnostic ID: 17 ===\n\n";

foreach($scores as $score) {
    echo "Module: " . $score->diagnosticmodule->titre . "\n";
    echo "Bloc ID: " . $score->diagnosticblocstatut_id . "\n";
    echo "Bloc Code: " . ($score->diagnosticblocstatut ? $score->diagnosticblocstatut->code : 'NULL') . "\n";
    echo "Score: " . $score->score_pourcentage . "%\n";
    echo "---\n";
}

echo "\n=== Tous les Diagnosticblocstatut ===\n";
$blocs = App\Models\Diagnosticblocstatut::all();
foreach($blocs as $bloc) {
    echo "ID: " . $bloc->id . " | Code: " . $bloc->code . " | Titre: " . $bloc->titre . "\n";
}
