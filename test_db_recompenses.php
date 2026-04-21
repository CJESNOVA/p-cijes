<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Recompense;
use App\Models\Alerte;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Membre;

echo "=== VÉRIFICATION BDD RÉCOMPENSES ===\n\n";

// Récupérer le membre test
$membre = Membre::find(17);
if (!$membre) {
    echo "Aucun membre trouvé\n";
    exit;
}

echo "Membre : {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n\n";

// Vérifier les récompenses récentes
echo "=== RÉCOMPENSES ===\n";
$recompenses = Recompense::where('membre_id', $membre->id)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($recompenses as $rec) {
    echo "ID: {$rec->id} | Valeur: {$rec->valeur} | Action: " . ($rec->action ? $rec->action->titre : 'N/A') . " | Date: {$rec->dateattribution}\n";
}

// Vérifier les alertes récentes
echo "\n=== ALERTES ===\n";
$alertes = Alerte::where('membre_id', $membre->id)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($alertes as $alerte) {
    echo "ID: {$alerte->id} | Titre: {$alerte->titre} | Lu: " . ($alerte->lu ? 'Oui' : 'Non') . " | Date: {$alerte->datealerte}\n";
}

// Vérifier les comptes ressources
echo "\n=== COMPTES RESSOURCES ===\n";
$comptes = Ressourcecompte::where('membre_id', $membre->id)
    ->orWhere('entreprise_id', $membre->entreprise_id)
    ->get();

foreach ($comptes as $compte) {
    echo "ID: {$compte->id} | Type: " . ($compte->ressourcetype ? $compte->ressourcetype->titre : 'N/A') . " | Solde: {$compte->solde}\n";
}

// Vérifier les transactions récentes
echo "\n=== TRANSACTIONS RÉCENTES ===\n";
$transactions = Ressourcetransaction::whereHas('ressourcecompte', function($query) use ($membre) {
        $query->where('membre_id', $membre->id)
              ->orWhere('entreprise_id', $membre->entreprise_id);
    })
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($transactions as $trans) {
    echo "ID: {$trans->id} | Montant: {$trans->montant} | Réf: {$trans->reference} | Date: {$trans->datetransaction}\n";
}

echo "\n=== STATISTIQUES ===\n";
echo "Total récompenses: " . Recompense::where('membre_id', $membre->id)->count() . "\n";
echo "Total alertes: " . Alerte::where('membre_id', $membre->id)->count() . "\n";
echo "Total comptes ressources: " . Ressourcecompte::where('membre_id', $membre->id)->count() . "\n";
echo "Total transactions: " . Ressourcetransaction::whereHas('ressourcecompte', function($query) use ($membre) {
        $query->where('membre_id', $membre->id);
    })->count() . "\n";

echo "\n=== FIN VÉRIFICATION ===\n";
