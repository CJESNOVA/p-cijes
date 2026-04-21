<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialiser l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== TEST COMPLET DU SYSTÈME DE RÉCOMPENSES ===\n\n";

try {
    // 1. Récupérer un utilisateur et membre existant pour les tests
    $user = User::find(9);
    if (!$user) {
        echo "❌ Aucun utilisateur trouvé. Créez d'abord un utilisateur.\n";
        exit(1);
    }

    $membre = Membre::where('user_id', $user->id)->first();
    if (!$membre) {
        echo "❌ Aucun membre trouvé pour l'utilisateur ID {$user->id}. Créez d'abord un profil membre.\n";
        exit(1);
    }

    echo "✅ Utilisateur trouvé : {$user->email} (ID: {$user->id})\n";
    echo "✅ Membre trouvé : {$membre->nom} {$membre->prenoms} (ID: {$membre->id})\n\n";

    // 2. Récupérer une entreprise pour les tests
    $entreprise = Entreprise::first();
    if ($entreprise) {
        echo "✅ Entreprise trouvée : {$entreprise->raison_sociale} (ID: {$entreprise->id})\n\n";
    } else {
        echo "⚠️  Aucune entreprise trouvée. Certains tests seront limités.\n\n";
    }

    // 3. Initialiser le service de récompenses
    $recompenseService = new RecompenseService();

    // 4. Tester toutes les actions de récompenses disponibles
    $actions = DB::table('actions')->where('etat', 1)->get();
    
    echo "=== TESTS DES RÉCOMPENSES ===\n";
    echo "Nombre d'actions à tester : " . $actions->count() . "\n\n";

    $testsExecutes = 0;
    $testsReussis = 0;
    $testsEchoues = 0;

    foreach ($actions as $action) {
        $testsExecutes++;
        echo "Test {$testsExecutes} : {$action->titre} (Code: {$action->code})\n";
        
        try {
            // Déterminer les paramètres selon le type d'action
            $entrepriseTest = null;
            $sourceId = null;
            $montant = null;

            // Actions spécifiques selon le code
            switch ($action->code) {
                case 'PAIEMENT_COTISATION':
                case 'RECHARGE_KOBO':
                case 'RECEPTION_SIKA':
                case 'RENOUVELLEMENT':
                    // Ces actions nécessitent un montant pour le calcul en pourcentage
                    $montant = 10000; // 10.000 F CFA
                    $entrepriseTest = $entreprise;
                    $sourceId = 'TEST_' . time() . '_' . $action->code;
                    break;
                    
                case 'PASSAGE_NIVEAU':
                case 'PASSAGE_PROFIL':
                case 'DIAG_DIRIGEANT':
                    // Actions liées aux diagnostics
                    $entrepriseTest = $entreprise;
                    $sourceId = 'TEST_DIAG_' . time();
                    $montant = 85; // Score de 85%
                    break;
                    
                case 'CONNEXION_50':
                    // Action personnelle
                    $sourceId = $membre->id;
                    break;
                    
                case 'INSCRIPTION':
                case 'PROFIL_COMPLET':
                case 'DEMANDE_SIKA':
                case 'PARRAINAGE':
                case 'TEST_CLASSIFICATION':
                    // Actions personnelles avec source
                    $sourceId = 'TEST_' . time() . '_' . $action->code;
                    break;
                    
                default:
                    // Action générique
                    $sourceId = 'TEST_GENERIC_' . time();
                    break;
            }

            // Exécuter l'attribution de récompense
            $recompense = $recompenseService->attribuerRecompense(
                $action->code,
                $membre,
                $entrepriseTest,
                $sourceId,
                $montant
            );

            if ($recompense) {
                $testsReussis++;
                echo "  ✅ SUCCÈS - Récompense ID: {$recompense->id}, Valeur: {$recompense->valeur} points\n";
                
                // Vérifier la création des ressources associées
                if ($action->ressourcetype_id) {
                    $compte = DB::table('ressourcecomptes')
                        ->where('membre_id', $membre->id)
                        ->where('ressourcetype_id', $action->ressourcetype_id)
                        ->when($entrepriseTest, function ($query) use ($entrepriseTest) {
                            return $query->where('entreprise_id', $entrepriseTest->id);
                        })
                        ->first();
                    
                    if ($compte) {
                        echo "  💰 Compte ressource ID: {$compte->id}, Solde: {$compte->solde}\n";
                    }
                }
                
                // Vérifier l'alerte
                $alerte = DB::table('alertes')
                    ->where('recompense_id', $recompense->id)
                    ->first();
                
                if ($alerte) {
                    echo "  🔔 Alerte ID: {$alerte->id} créée avec succès\n";
                }
                
            } else {
                $testsEchoues++;
                echo "  ❌ ÉCHEC - La récompense n'a pas pu être attribuée (limite atteinte ?)\n";
            }

        } catch (\Exception $e) {
            $testsEchoues++;
            echo "  ❌ ERREUR - " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }

    // 5. Afficher le résumé des tests
    echo "=== RÉSUMÉ DES TESTS ===\n";
    echo "Tests exécutés : {$testsExecutes}\n";
    echo "Tests réussis : {$testsReussis}\n";
    echo "Tests échoués : {$testsEchoues}\n";
    echo "Taux de succès : " . round(($testsReussis / $testsExecutes) * 100, 2) . "%\n\n";

    // 6. Afficher les statistiques des récompenses
    echo "=== STATISTIQUES DES RÉCOMPENSES ===\n";
    
    $totalRecompenses = DB::table('recompenses')->count();
    $recompensesMembre = DB::table('recompenses')->where('membre_id', $membre->id)->count();
    $recompensesEntreprise = $entreprise ? DB::table('recompenses')->where('entreprise_id', $entreprise->id)->count() : 0;
    
    echo "Total des récompenses dans la base : {$totalRecompenses}\n";
    echo "Récompenses du membre test : {$recompensesMembre}\n";
    if ($entreprise) {
        echo "Récompenses de l'entreprise test : {$recompensesEntreprise}\n";
    }
    
    // 7. Afficher les dernières récompenses créées
    echo "\n=== DERNIÈRES RÉCOMPENSES CRÉÉES ===\n";
    $dernieresRecompenses = DB::table('recompenses')
        ->join('actions', 'recompenses.action_id', '=', 'actions.id')
        ->select('recompenses.*', 'actions.titre as action_titre', 'actions.code as action_code')
        ->orderBy('recompenses.created_at', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($dernieresRecompenses as $rec) {
        echo "- {$rec->action_titre} ({$rec->action_code}): {$rec->valeur} points - " . date('d/m/Y H:i:s', strtotime($rec->created_at)) . "\n";
    }

    echo "\n=== TEST TERMINÉ ===\n";

} catch (\Exception $e) {
    echo "❌ ERREUR GLOBALE : " . $e->getMessage() . "\n";
    echo "Stack trace :\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
