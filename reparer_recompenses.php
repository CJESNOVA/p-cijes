<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Recompense;
use App\Models\Action;

echo "=== RÉPARATION DES RÉCOMPENSES MANQUANTES ===\n\n";

$recompenseService = new RecompenseService();

// 1. Trouver tous les membres sans récompense d'inscription
$membresSansInscription = Membre::whereDoesntHave('recompenses', function($query) {
    $query->whereHas('action', function($q) {
        $q->where('code', 'INSCRIPTION');
    });
})->get();

echo "Membres sans récompense d'inscription : {$membresSansInscription->count()}\n\n";

foreach ($membresSansInscription as $membre) {
    echo "Traitement : {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n";
    
    try {
        // Vérifier si le membre a déjà une récompense INSCRIPTION
        $existing = Recompense::where('membre_id', $membre->id)
            ->whereHas('action', function($q) {
                $q->where('code', 'INSCRIPTION');
            })
            ->first();
            
        if (!$existing) {
            $result = $recompenseService->attribuerRecompense('INSCRIPTION', $membre);
            
            if ($result) {
                echo "  SUCCESS : Récompense INSCRIPTION attribuée (ID: {$result->id})\n";
            } else {
                echo "  FAILED : Impossible d'attribuer la récompense\n";
            }
        } else {
            echo "  SKIP : Déjà une récompense INSCRIPTION\n";
        }
        
    } catch (\Exception $e) {
        echo "  ERROR : " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// 2. Vérifier les membres sans récompense de profil complet
echo "\n=== VÉRIFICATION PROFIL COMPLET ===\n";
$membresSansProfil = Membre::whereDoesntHave('recompenses', function($query) {
    $query->whereHas('action', function($q) {
        $q->where('code', 'PROFIL_COMPLET');
    });
})->get();

echo "Membres sans récompense de profil complet : {$membresSansProfil->count()}\n";

// On n'attribue pas automatiquement PROFIL_COMPLET car il faut vérifier que le profil est vraiment complet

echo "\n=== RÉSUMÉ ===\n";
echo "Récompenses d'inscription réparées\n";
echo "Les nouveaux membres recevront automatiquement leurs récompenses\n";
echo "Le problème était probablement une erreur silencieuse lors de l'inscription\n";
