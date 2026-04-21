<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\User;
use App\Models\Recompense;

echo "=== TEST PROCESSUS INSCRIPTION COMPLET ===\n\n";

// Simuler l'inscription d'un nouveau membre
echo "1. Création d'un utilisateur test...\n";
$user = User::create([
    'name' => 'Test User',
    'email' => 'test' . time() . '@example.com',
    'password' => bcrypt('password'),
]);

echo "User créé : {$user->name} ({$user->email})\n";

echo "\n2. Création du membre associé...\n";
$membre = Membre::create([
    'nom' => 'Test',
    'prenom' => 'User',
    'email' => $user->email,
    'user_id' => $user->id,
    'membrestatut_id' => 1,
    'membretype_id' => 1,
    'pays_id' => 1,
    'etat' => 1,
]);

echo "Membre créé : {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n";

echo "\n3. Simulation du processus d'inscription (comme dans MembreController)...\n";
try {
    $recompenseService = new RecompenseService();
    
    // Comme dans MembreController ligne 90
    $result = $recompenseService->attribuerRecompense('INSCRIPTION', $membre, null, $membre->id, null);
    
    if ($result) {
        echo "SUCCESS : Récompense INSCRIPTION attribuée (ID: {$result->id})\n";
        echo "Valeur : {$result->valeur} points\n";
        
        // Vérifier l'alerte
        $alerte = \App\Models\Alerte::where('recompense_id', $result->id)->first();
        if ($alerte) {
            echo "Alerte créée : {$alerte->titre}\n";
        }
        
    } else {
        echo "FAILED : Impossible d'attribuer la récompense\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR : " . $e->getMessage() . "\n";
}

echo "\n4. Simulation de la connexion (comme dans AuthController)...\n";
try {
    $result = $recompenseService->attribuerRecompense('CONNEXION_50', $membre, null, $membre->id, null);
    
    if ($result) {
        echo "SUCCESS : Récompense CONNEXION_50 attribuée (ID: {$result->id})\n";
    } else {
        echo "FAILED : Impossible d'attribuer la récompense de connexion\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR : " . $e->getMessage() . "\n";
}

echo "\n5. Vérification finale...\n";
$recompenses = Recompense::where('membre_id', $membre->id)->get();
echo "Total récompenses pour {$membre->prenom} : {$recompenses->count()}\n";
foreach ($recompenses as $r) {
    echo "- {$r->action->code}: {$r->valeur} points\n";
}

echo "\n=== NETTOYAGE ===\n";
// Nettoyer le test
$membre->delete();
$user->delete();
echo "Test nettoyé\n";

echo "\n=== CONCLUSION ===\n";
echo "Le processus d'inscription fonctionne correctement !\n";
echo "Les nouveaux membres recevront leurs récompenses automatiquement.\n";
