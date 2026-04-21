<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Ajout compatibilité type 2 -> modules ===\n";

try {
    // Vérifier si la compatibilité existe déjà
    $exists = DB::table('ressourcetypeoffretypes')
        ->where('ressourcetype_id', 2)
        ->where('offretype_id', 3)
        ->exists();
    
    if ($exists) {
        echo "La compatibilité type 2 -> modules existe déjà\n";
    } else {
        // Ajouter la compatibilité
        DB::table('ressourcetypeoffretypes')->insert([
            'ressourcetype_id' => 2,
            'offretype_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "Compatibilité type 2 -> modules ajoutée avec succès !\n";
    }
    
    // Vérifier après ajout
    $compat = DB::table('ressourcetypeoffretypes')
        ->where('ressourcetype_id', 2)
        ->where('offretype_id', 3)
        ->exists();
    
    echo "Vérification: " . ($compat ? 'COMPATIBLE' : 'NON COMPATIBLE') . PHP_EOL;
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . PHP_EOL;
}
