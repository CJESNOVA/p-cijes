<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Tables avec 'resource' ou 'offre' ===\n";

$tables = DB::select('SHOW TABLES');

foreach ($tables as $table) {
    foreach ($table as $value) {
        if (strpos($value, 'resource') !== false || strpos($value, 'offre') !== false) {
            echo $value . PHP_EOL;
        }
    }
}

echo "\n=== Vérification du modèle Ressourcetypeoffretype ===\n";

try {
    $model = new \App\Models\Ressourcetypeoffretype();
    echo "Modèle Ressourcetypeoffretype trouvé\n";
    echo "Table: " . $model->getTable() . PHP_EOL;
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . PHP_EOL;
}
