<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\User;

echo "=== DÉBOGAGE PROCESSUS INSCRIPTION ===\n\n";

// Le problème : pourquoi Japhet n'a pas eu sa récompense lors de l'inscription ?

echo "Hypothèses possibles :\n";
echo "1. Le code de récompense n'était pas encore implémenté le 2026-04-01\n";
echo "2. Il y a eu une erreur silencieuse lors de l'inscription\n";
echo "3. La limite était déjà atteinte par un autre membre\n";
echo "4. Le membre n'avait pas d'user_id valide\n\n";

// Vérifier la date de création de l'action INSCRIPTION
$inscription = \App\Models\Action::where('code', 'INSCRIPTION')->first();
if ($inscription) {
    echo "Action INSCRIPTION créée le : {$inscription->created_at}\n";
    echo "Japhet inscrit le : 2026-04-01 03:41:31\n";
    
    if ($inscription->created_at > '2026-04-01 03:41:31') {
        echo "PROBLÈME : L'action a été créée APRES l'inscription de Japhet !\n";
    } else {
        echo "L'action existait déjà à l'inscription de Japhet\n";
    }
}

echo "\nVérification des récompenses du 2026-04-01 :\n";
$rewards = \App\Models\Recompense::whereDate('created_at', '2026-04-01')->get();
echo "Récompenses créées ce jour : {$rewards->count()}\n";

foreach ($rewards as $r) {
    echo "- {$r->id}: {$r->membre->prenom} {$r->membre->nom} - {$r->action->code} ({$r->created_at})\n";
}

echo "\n=== SOLUTION ===\n";
echo "Le problème est probablement que le code de récompense :\n";
echo "- N'était pas encore implémenté lors de l'inscription de Japhet\n";
echo "- OU a échoué silencieusement\n\n";

echo "Pour corriger :\n";
echo "1. Attribuer manuellement les récompenses manquantes\n";
echo "2. Ajouter des logs d'erreur dans le processus d'inscription\n";
echo "3. Créer une commande pour réparer les comptes\n";
