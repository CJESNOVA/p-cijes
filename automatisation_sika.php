<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialiser l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ressourcetransaction;
use App\Models\Ressourcecompte;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Services\RecompenseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== AUTOMATISATION DU TRAITEMENT DES TRANSACTIONS SIKA ===\n";
echo "Date d'exécution : " . date('Y-m-d H:i:s') . "\n\n";

// Vérification préliminaire : l'action RECEPTION_SIKA doit exister
$action = DB::table('actions')->where('code', 'RECEPTION_SIKA')->first();
if (!$action) {
    echo "❌ ERREUR: L'action RECEPTION_SIKA n'existe pas dans la base de données !\n";
    echo "   Veuillez créer cette action avant de continuer.\n";
    exit(1);
}

// Vérification du seuil de pourcentage
if (!$action->seuil || !str_contains($action->seuil, '%')) {
    echo "⚠️ ATTENTION: L'action RECEPTION_SIKA n'a pas de seuil de pourcentage correct.\n";
    echo "   Seuil actuel: " . ($action->seuil ?? 'NULL') . "\n";
    echo "   Correction automatique du seuil à 25%...\n";
    DB::table('actions')->where('code', 'RECEPTION_SIKA')->update(['seuil' => '25%']);
    echo "   ✅ Seuil corrigé à 25%\n\n";
}

echo "✅ Action RECEPTION_SIKA vérifiée:\n";
echo "   ID: " . $action->id . "\n";
echo "   Titre: " . $action->titre . "\n";
echo "   Points: " . $action->point . "\n";
echo "   Seuil: " . $action->seuil . "\n\n";

try {
    // 1. Récupérer toutes les transactions SIKA (ressourcetype_id = 4) validées
    //    - etat = 1 (validée)
    //    - spotlight = 1 (à traiter)
    $transactionsATraiter = Ressourcetransaction::with(['ressourcecompte'])
        ->whereHas('ressourcecompte', function ($query) {
            $query->where('ressourcetype_id', 4); // Type SIKA
        })
        ->where('etat', 1) // Transactions validées
        ->where('spotlight', 1) // À traiter
        ->get();

    echo "📊 Transactions SIKA trouvées à traiter : " . $transactionsATraiter->count() . "\n";

    if ($transactionsATraiter->isEmpty()) {
        echo "✅ Aucune transaction SIKA à traiter.\n";
        
        // Option: créer une transaction de test si --test est passé en paramètre
        if (in_array('--test', $argv) || in_array('--create-test', $argv)) {
            echo "\n🧪 Création d'une transaction de test...\n";
            
            // Trouver un compte SIKA existant
            $compteSika = DB::table('ressourcecomptes')
                ->where('ressourcetype_id', 4)
                ->first();
                
            if (!$compteSika) {
                echo "❌ Aucun compte SIKA trouvé. Création d'un nouveau compte...\n";
                $compteSikaId = DB::table('ressourcecomptes')->insertGetId([
                    'membre_id' => 1,
                    'entreprise_id' => null,
                    'ressourcetype_id' => 4,
                    'solde' => 0,
                    'spotlight' => 0,
                    'etat' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $compteSikaId = $compteSika->id;
            }
            
            // Créer la transaction de test
            $reference = 'TEST-SIKA-' . strtoupper(Str::random(8));
            $montant = 10000;
            
            $transactionId = DB::table('ressourcetransactions')->insertGetId([
                'montant' => $montant,
                'reference' => $reference,
                'ressourcecompte_id' => $compteSikaId,
                'datetransaction' => now(),
                'operationtype_id' => 1,
                'description' => 'Transaction de test pour vérifier le système SIKA',
                'spotlight' => 1,
                'etat' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "✅ Transaction de test créée:\n";
            echo "   ID: " . $transactionId . "\n";
            echo "   Référence: " . $reference . "\n";
            echo "   Montant: " . number_format($montant, 0, ',', ' ') . " FCFA\n";
            echo "   Calcul attendu: " . number_format($montant * 0.25, 0, ',', ' ') . " points (25%)\n\n";
            
            echo "🔄 Relancement du traitement...\n\n";
            
            // Relancer le traitement avec la nouvelle transaction
            $transactionsATraiter = Ressourcetransaction::with(['ressourcecompte'])
                ->whereHas('ressourcecompte', function ($query) {
                    $query->where('ressourcetype_id', 4);
                })
                ->where('etat', 1)
                ->where('spotlight', 1)
                ->get();
        } else {
            echo "\n💡 Pour créer une transaction de test, utilisez: php automatisation_sika.php --test\n";
            exit(0);
        }
    }

    // 2. Initialiser le service de récompenses
    $recompenseService = new RecompenseService();

    // 3. Traiter chaque transaction
    $traitees = 0;
    $recompensesAttribuees = 0;
    $erreurs = 0;

    foreach ($transactionsATraiter as $transaction) {
        try {
            echo "🔍 Traitement transaction ID: {$transaction->id} (Réf: {$transaction->reference})\n";

            // Récupérer le membre et l'entreprise
            $membre = null;
            $entreprise = null;

            if ($transaction->ressourcecompte) {
                $membre = Membre::find($transaction->ressourcecompte->membre_id);
                if ($transaction->ressourcecompte->entreprise_id) {
                    $entreprise = Entreprise::find($transaction->ressourcecompte->entreprise_id);
                }
            }

            if (!$membre) {
                echo "   ⚠️ Membre non trouvé, transaction ignorée\n";
                $erreurs++;
                continue;
            }

            // Attribuer la récompense RECEPTION_SIKA
            $recompense = $recompenseService->attribuerRecompense(
                'RECEPTION_SIKA',
                $membre,
                $entreprise,
                $transaction->id,
                $transaction->montant // Utiliser le montant comme base pour calcul %
            );

            if ($recompense) {
                echo "   ✅ Récompense RECEPTION_SIKA attribuée (ID: {$recompense->id}, Points: {$recompense->valeur})\n";

                // Mettre à jour le solde du compte ressource
                $compte = $transaction->ressourcecompte;
                $compte->increment('solde', $transaction->montant);
                
                echo "   💰 Solde du compte mis à jour: +{$transaction->montant} FCFA\n";

                // Mettre à jour le spotlight de la transaction à 0
                $transaction->update(['spotlight' => 0]);

                $recompensesAttribuees++;
            } else {
                echo "   ❌ Échec de l'attribution de récompense\n";
                $erreurs++;
            }

            $traitees++;

        } catch (\Exception $e) {
            echo "   ❌ Erreur lors du traitement: " . $e->getMessage() . "\n";
            Log::error("Erreur traitement transaction SIKA", [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            $erreurs++;
        }
    }

    // 4. Résumé final
    echo "\n=== RÉSUMÉ DU TRAITEMENT AUTOMATISÉ ===\n";
    echo "📊 Transactions trouvées: " . $transactionsATraiter->count() . "\n";
    echo "🔄 Transactions traitées: {$traitees}\n";
    echo "🎁 Récompenses attribuées: {$recompensesAttribuees}\n";
    echo "💰 Soldes de comptes mis à jour: {$recompensesAttribuees}\n";
    echo "❌ Erreurs: {$erreurs}\n";
    echo "📈 Taux de succès: " . round(($recompensesAttribuees / $traitees) * 100, 2) . "%\n";

    // 5. Journalisation dans un fichier log
    $logData = [
        'date_execution' => date('Y-m-d H:i:s'),
        'transactions_trouvees' => $transactionsATraiter->count(),
        'transactions_traitees' => $traitees,
        'recompenses_attribuees' => $recompensesAttribuees,
        'soldes_mis_a_jour' => $recompensesAttribuees,
        'erreurs' => $erreurs,
        'taux_succes' => round(($recompensesAttribuees / $traitees) * 100, 2)
    ];

    $logFile = __DIR__ . '/sika_automation.log';
    $logContent = date('Y-m-d H:i:s') . " - " . json_encode($logData) . "\n";
    file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);

    echo "\n📝 Journal sauvegardé dans: sika_automation.log\n";

    // 6. Résumé des récompenses SIKA existantes
    echo "\n=== RÉSUMÉ DES RÉCOMPENSES SIKA EXISTANTES ===\n";
    $recompensesSika = DB::table('recompenses')
        ->join('actions', 'recompenses.action_id', '=', 'actions.id')
        ->whereIn('actions.code', ['DEMANDE_SIKA', 'RECEPTION_SIKA'])
        ->select('recompenses.*', 'actions.code as action_code', 'actions.titre as action_titre')
        ->orderBy('recompenses.created_at', 'desc')
        ->limit(10)
        ->get();

    if ($recompensesSika->isEmpty()) {
        echo "📊 Aucune récompense SIKA trouvée dans la base de données.\n";
    } else {
        echo "📊 " . $recompensesSika->count() . " dernières récompenses SIKA:\n";
        foreach ($recompensesSika as $recompense) {
            echo "   " . $recompense->action_code . " - " . number_format($recompense->valeur, 0, ',', ' ') . " points";
            echo " (Membre " . $recompense->membre_id . ") - " . $recompense->created_at . "\n";
        }
    }

    // 7. Statistiques globales
    echo "\n=== STATISTIQUES GLOBALES SIKA ===\n";
    $stats = [
        'DEMANDE_SIKA' => DB::table('recompenses')
            ->join('actions', 'recompenses.action_id', '=', 'actions.id')
            ->where('actions.code', 'DEMANDE_SIKA')
            ->count(),
        'RECEPTION_SIKA' => DB::table('recompenses')
            ->join('actions', 'recompenses.action_id', '=', 'actions.id')
            ->where('actions.code', 'RECEPTION_SIKA')
            ->count(),
        'total_points_reception' => DB::table('recompenses')
            ->join('actions', 'recompenses.action_id', '=', 'actions.id')
            ->where('actions.code', 'RECEPTION_SIKA')
            ->sum('recompenses.valeur')
    ];

    echo "📈 Récompenses DEMANDE_SIKA: " . $stats['DEMANDE_SIKA'] . "\n";
    echo "📈 Récompenses RECEPTION_SIKA: " . $stats['RECEPTION_SIKA'] . "\n";
    echo "💰 Total points RECEPTION_SIKA: " . number_format($stats['total_points_reception'], 0, ',', ' ') . "\n";

    if ($recompensesAttribuees > 0) {
        echo "\n🎉 Automatisation terminée avec succès !\n";
    }

} catch (\Exception $e) {
    echo "❌ ERREUR GLOBALE: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Journaliser l'erreur
    $logFile = __DIR__ . '/sika_automation.log';
    $errorLog = [
        'date_execution' => date('Y-m-d H:i:s'),
        'erreur' => $e->getMessage(),
        'type' => 'CRITICAL_ERROR'
    ];
    $logContent = date('Y-m-d H:i:s') . " - " . json_encode($errorLog) . "\n";
    file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);
    
    exit(1);
}

echo "\n=== AUTOMATISATION TERMINÉE ===\n";
