<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Vérification des comptes ressources du membre 1 ===\n\n";

$comptes = \App\Models\Ressourcecompte::where('membre_id', 1)
    ->where('etat', 1)
    ->get();

echo "Comptes trouvés : " . $comptes->count() . "\n\n";

foreach ($comptes as $compte) {
    echo "Type ID: " . $compte->ressourcetype_id . "\n";
    echo "Solde: " . $compte->solde . "\n";
    echo "ID: " . $compte->id . "\n";
    echo "Entreprise ID: " . ($compte->entreprise_id ?? 'NULL') . "\n";
    echo "---\n";
}

echo "\n=== Vérification compatibilité avec modules ressources ===\n";

$priorites = [2, 3, 4, 1];
$montantRequis = 2500;

foreach ($priorites as $typeId) {
    echo "\nRecherche type $typeId:\n";
    
    $compte = \App\Models\Ressourcecompte::where('ressourcetype_id', $typeId)
        ->where('membre_id', 1)
        ->where('etat', 1)
        ->where('solde', '>=', $montantRequis)
        ->first();

    if ($compte) {
        echo "  - Compte trouvé: ID " . $compte->id . " - Solde: " . $compte->solde . "\n";
        
        // Vérifier la compatibilité
        $isCompatible = \App\Models\Ressourcetypeoffretype::where('ressourcetype_id', $typeId)
            ->where('offretype_id', 3) // 3 = modules ressources
            ->exists();

        echo "  - Compatible avec modules: " . ($isCompatible ? 'OUI' : 'NON') . "\n";
        
        if ($isCompatible) {
            echo "  - >>> SERAIT UTILISÉ <<<\n";
        }
    } else {
        echo "  - Aucun compte trouvé\n";
    }
}
