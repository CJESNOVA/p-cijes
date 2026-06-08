<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Membre;
use App\Models\Action;

echo "=== DÉBOGAGE PRODUCTION ===\n\n";

// 1. Vérifier les actions disponibles
$inscription = Action::where('code', 'INSCRIPTION')->first();
$connexion = Action::where('code', 'CONNEXION_50')->first();

echo "Action INSCRIPTION : " . ($inscription ? "EXISTE (limite: {$inscription->limite})" : "N'EXISTE PAS") . "\n";
echo "Action CONNEXION_50 : " . ($connexion ? "EXISTE (limite: {$connexion->limite})" : "N'EXISTE PAS") . "\n\n";

// 2. Vérifier les membres récents
$membres = Membre::orderBy('created_at', 'desc')->limit(3)->get();
echo "Membres récents :\n";
foreach ($membres as $m) {
    $rewards = \App\Models\Recompense::where('membre_id', $m->id)->count();
    echo "- {$m->prenom} {$m->nom} (créé: {$m->created_at}) - {$rewards} récompenses\n";
}

echo "\n=== PROBLÈMES IDENTIFIÉS ===\n";
echo "1. Limite INSCRIPTION = 1 (déjà atteinte)\n";
echo "2. CONNEXION_50 s'attribue à CHAQUE connexion (limite 50)\n";
echo "3. Pas de vérification si utilisateur a déjà les récompenses\n";

echo "\n=== SOLUTIONS ===\n";
echo "1. Augmenter limite INSCRIPTION ou créer action INSCRIPTION_NOUVEAU\n";
echo "2. Ajouter logique pour éviter doublons\n";
echo "3. Vérifier logs erreurs dans storage/logs/laravel.log\n";
