<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Membre;
use App\Models\Alerte;
use App\Models\User;

echo "=== TEST API ALERTES ===\n\n";

// Simuler l'authentification
$user = User::first();
if (!$user) {
    echo "Aucun utilisateur trouvé\n";
    exit;
}

echo "Utilisateur : {$user->name} (ID: {$user->id})\n";

// Vérifier la relation membre
$membre = $user->membre;
if (!$membre) {
    echo "Le membre n'existe pas pour cet utilisateur\n";
    exit;
}

echo "Membre : {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n\n";

// Tester la route alertes
echo "=== ALERTES DU MEMBRE ===\n";
$alertes = $membre->alertes()->orderByDesc('datealerte')->get();
$unreadCount = $membre->alertes()->where('lu', 0)->count();

echo "Nombre total d'alertes : " . $alertes->count() . "\n";
echo "Nombre non lues : {$unreadCount}\n\n";

echo "=== DÉTAIL DES ALERTES ===\n";
foreach ($alertes->take(3) as $alerte) {
    echo "ID: {$alerte->id}\n";
    echo "Titre: {$alerte->titre}\n";
    echo "Contenu: {$alerte->contenu}\n";
    echo "Lien: {$alerte->lienurl}\n";
    echo "Date: {$alerte->datealerte}\n";
    echo "Lu: " . ($alerte->lu ? 'Oui' : 'Non') . "\n";
    echo "---\n";
}

// Simuler la réponse JSON
$response = [
    'alertes' => $alertes->map(function($alerte) {
        return [
            'id' => $alerte->id,
            'title' => $alerte->titre,
            'message' => $alerte->contenu,
            'lienurl' => $alerte->lienurl,
            'lu' => $alerte->lu,
            'datealerte' => $alerte->datealerte
        ];
    }),
    'unreadCount' => $unreadCount
];

echo "\n=== RÉPONSE JSON SIMULÉE ===\n";
echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

echo "\n=== FIN TEST ===\n";
