<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Vérification compatibilité types ressources ===\n\n";

echo "Type 2 compatible avec modules (offretype_id = 3): ";
$compat2 = \App\Models\Ressourcetypeoffretype::where('ressourcetype_id', 2)
    ->where('offretype_id', 3)
    ->exists();
echo $compat2 ? 'OUI' : 'NON';
echo "\n\n";

echo "Type 4 compatible avec modules (offretype_id = 3): ";
$compat4 = \App\Models\Ressourcetypeoffretype::where('ressourcetype_id', 4)
    ->where('offretype_id', 3)
    ->exists();
echo $compat4 ? 'OUI' : 'NON';
echo "\n\n";

echo "=== Types disponibles pour chaque ressource type ===\n";

$types = [2, 4];
foreach ($types as $typeId) {
    echo "\nType $typeId compatible avec:\n";
    
    $compatibilites = \App\Models\Ressourcetypeoffretype::where('ressourcetype_id', $typeId)->get();
    
    if ($compatibilites->isEmpty()) {
        echo "  - Aucune compatibilité définie\n";
    } else {
        foreach ($compatibilites as $compat) {
            echo "  - Offre type ID: " . $compat->offretype_id . "\n";
        }
    }
}

echo "\n=== Solution : Ajouter la compatibilité manquante ===\n";

if (!$compat2) {
    echo "Le type 2 n'est pas compatible avec les modules ressources.\n";
    echo "Pour corriger, ajoutez cette entrée dans la table resourcetypeoffretypes:\n";
    echo "INSERT INTO resourcetypeoffretypes (ressourcetype_id, offretype_id) VALUES (2, 3);\n";
} else {
    echo "Le type 2 est déjà compatible.\n";
}
