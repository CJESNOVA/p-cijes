<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Diagnostic;
use App\Models\Diagnosticmodule;
use App\Models\Diagnosticquestion;
use App\Models\Diagnosticreponse;
use App\Models\Diagnosticresultat;
use App\Models\Diagnosticmodulescore;
use App\Models\Plantemplate;
use App\Models\Plan;
use App\Models\Membre;
use App\Models\Accompagnement;
use App\Models\Diagnosticevolution;

use App\Services\RecompenseService;
use App\Services\DiagnosticStatutService;

class DiagnosticController extends Controller
{
    protected $diagnosticStatutService;

    public function __construct(DiagnosticStatutService $diagnosticStatutService)
    {
        $this->diagnosticStatutService = $diagnosticStatutService;
    }

    public function showForm($moduleId = null)
    {
        $userId = Auth::id();
        
    // Vérification du membre connecté
    $membre = Membre::where('user_id', $userId)->first();
    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
    }

    // Récupération de TOUS les modules type 1 (diagnostic PME), triés par position
    $allDiagnosticmodules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
        ->where('etat', 1)
        ->orderBy('position')
        ->with(['diagnosticquestions' => function ($q) {
            $q->where('etat', 1)
                ->orderByRaw('CAST(position AS UNSIGNED)') // Cast en nombre pour tri numérique
                ->with(['diagnosticreponses' => function ($query) {
                    $query->where('etat', 1)
                            ->inRandomOrder(); // mélange aléatoire des réponses
                }]);
        }])
        ->get();

    // Si aucun moduleId spécifié, prendre le premier
    if ($moduleId === null) {
        $currentModule = $allDiagnosticmodules->first();
        $moduleId = $currentModule ? $currentModule->id : null;
    } else {
        $currentModule = $allDiagnosticmodules->where('id', $moduleId)->first();
    }

    // Récupérer tous les modules pour la navigation
    $modules = $allDiagnosticmodules;
    
    // DEBUG: Vérifier le contenu de la collection
    $debugModules = $allDiagnosticmodules->map(function($module) {
        return ['id' => $module->id, 'titre' => $module->titre];
    })->toArray();
    
    $currentModuleIndex = 0;
    if ($currentModule) {
        // DEBUG: Vérifier $currentModule
        $debugCurrent = [
            'currentModule_id' => $currentModule->id,
            'currentModule_titre' => $currentModule->titre,
            'currentModule_exists' => isset($currentModule),
            'currentModule_class' => get_class($currentModule)
        ];
        
        // Approche ultra-simple
        $moduleIds = $allDiagnosticmodules->pluck('id')->toArray();
        $targetId = (int)$currentModule->id;
        
        foreach ($moduleIds as $index => $moduleId) {
            if ((int)$moduleId === $targetId) {
                $currentModuleIndex = $index;
                break;
            }
        }
    }
    
    $nextModule = $currentModule ? $allDiagnosticmodules->get($currentModuleIndex + 1) : null;
    $previousModule = $currentModuleIndex > 0 ? $allDiagnosticmodules->get($currentModuleIndex - 1) : null;
    $isLastModule = $currentModule ? ($currentModuleIndex + 1) >= $allDiagnosticmodules->count() : false;
    
    // DEBUG: Vérifier la transmission des variables
    $debugTransmission = [
        'currentModuleIndex_before_view' => $currentModuleIndex,
        'isLastModule_before_view' => $isLastModule,
        'nextModule_before_view' => $nextModule ? $nextModule->id : null,
        'previousModule_before_view' => $previousModule ? $previousModule->id : null
    ];
    
    // Si c'est le dernier module, définir la session pour finalisation
    if ($isLastModule) {
        session(['showFinalization' => true]);
    }

    // Récupérer le dernier diagnostic en cours pour ce membre (non terminé)
    $diagnostic = Diagnostic::where('membre_id', $membre->id)
        ->where('entreprise_id', 0)
        ->where('diagnostictype_id', 1) 
        ->where('diagnosticstatut_id', 1) // Non terminé
        ->orderBy('created_at', 'desc')
        ->first();

    // Préparer les réponses existantes (déjà cochées)
    $existing = [];
    if ($diagnostic) {
        $existing = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->get()
            ->groupBy('diagnosticquestion_id')
            ->map(fn($items) => $items->pluck('diagnosticreponse_id')->toArray())
            ->toArray(); // convertir en array pour Blade
    }

    return view('diagnostic.form', compact(
        'modules',
        'currentModule',
        'nextModule',
        'previousModule',
        'isLastModule',
        'existing',
        'diagnostic',
        'membre'
    ));
}




    public function saveModule(Request $request, $moduleId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
        }

        // 🔍 DEBUG: Vérifier les données reçues
        $debugData = [
            'moduleId' => $moduleId,
            'answers_received' => $request->reponses,
            'answers_count' => count($request->reponses ?? []),
            'answers_is_array' => is_array($request->reponses ?? []),
            'all_answers_keys' => array_keys($request->reponses ?? [])
        ];

        // 🔍 Vérifier si au moins une réponse a été fournie
        $answers = $request->reponses ?? [];
        if (empty($answers) || !is_array($answers)) {
            return redirect()->back()
                ->with('error', '⚠️ Veuillez répondre à au moins une question avant de continuer.')
                ->withInput();
        }

        // 🔍 Vérifier si les réponses contiennent des valeurs valides
        $hasValidAnswers = false;
        foreach ($answers as $questionId => $reponseData) {
            if (is_array($reponseData)) {
                if (!empty(array_filter($reponseData))) {
                    $hasValidAnswers = true;
                    break;
                }
            } elseif (!empty($reponseData)) {
                $hasValidAnswers = true;
                break;
            }
        }

        if (!$hasValidAnswers) {
            return redirect()->back()
                ->with('error', '⚠️ Veuillez cocher au moins une réponse avant de continuer.')
                ->withInput();
        }

        // Récupérer le dernier diagnostic en cours pour ce membre (non terminé)
        $diagnostic = Diagnostic::where('membre_id', $membre->id)
            ->where('entreprise_id', 0)
            ->where('diagnostictype_id', 1) 
            ->where('diagnosticstatut_id', 1) // Non terminé
            ->orderBy('created_at', 'desc')
            ->first();

        // Si aucun diagnostic en cours, en créer un nouveau
        if (!$diagnostic) {
            $diagnostic = Diagnostic::create([
                'membre_id' => $membre->id,
                'entreprise_id' => 0,
                'diagnosticstatut_id' => 1,
                'diagnostictype_id' => 1,
                'scoreglobal' => 0,
                'etat' => 1,
            ]);
        }

        // 🔍 Récupérer les questions obligatoires pour ce module
        $module = Diagnosticmodule::find($moduleId);
        $moduleQuestions = $module->diagnosticquestions()
            ->where('etat', 1)
            ->get();
            
        $obligatoires = $moduleQuestions->where('obligatoire', 1)->pluck('id')->toArray();
        
        // 🔍 Vérifier si les questions obligatoires sont répondues
        $repondues = [];
        foreach ($answers as $questionId => $reponseData) {
            if (is_array($reponseData)) {
                if (!empty(array_filter($reponseData))) {
                    $repondues[] = $questionId;
                }
            } elseif (!empty($reponseData)) {
                $repondues[] = $questionId;
            }
        }
        
        $obligatoiresManquantes = array_diff($obligatoires, $repondues);
        if (!empty($obligatoiresManquantes)) {
            $nbManquantes = count($obligatoiresManquantes);
            // Récupérer la position du module pour l'afficher
            $allModules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
                ->where('etat', 1)
                ->orderBy('position')
                ->get();
            $modulePosition = $allModules->search(function($mod) use ($moduleId) {
                return $mod->id == $moduleId;
            }) + 1;
            $totalModules = $allModules->count();
            
            return redirect()->back()
                ->with('warning', "⚠️ Module {$modulePosition}/{$totalModules} : Il reste {$nbManquantes} question(s) obligatoire(s) non remplie(s). Veuillez compléter avant de continuer.")
                ->withInput();
        }

        // 🔄 Utiliser une transaction pour la cohérence des données
        \DB::transaction(function () use ($diagnostic, $moduleId, $answers) {
            // Supprimer les anciens résultats pour ce module uniquement
            $moduleQuestionIds = Diagnosticmodule::find($moduleId)
                ->diagnosticquestions()
                ->pluck('id')
                ->toArray();
                
            Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->whereIn('diagnosticquestion_id', $moduleQuestionIds)
                ->delete();

            // Enregistrer les nouvelles réponses pour ce module
            foreach ($answers as $questionId => $reponseData) {
                // Si c'est un tableau (checkbox), traiter chaque élément
                if (is_array($reponseData)) {
                    foreach ($reponseData as $reponseId) {
                        Diagnosticresultat::create([
                            'diagnostic_id' => $diagnostic->id,
                            'diagnosticquestion_id' => $questionId,
                            'diagnosticreponse_id' => $reponseId,
                            'etat' => 1,
                        ]);
                    }
                } 
                // Si c'est une valeur simple (radio), traiter directement
                elseif ($reponseData) {
                    Diagnosticresultat::create([
                        'diagnostic_id' => $diagnostic->id,
                        'diagnosticquestion_id' => $questionId,
                        'diagnosticreponse_id' => $reponseData,
                        'etat' => 1,
                    ]);
                }
            }
        });

        // Calculer et enregistrer le score total cumulé du module
        $scoreCalcule = $this->calculerScoreTotalModule($diagnostic->id, $moduleId);
        
        // Créer ou mettre à jour le score du module avec le score cumulé
        $moduleScore = Diagnosticmodulescore::updateOrCreate(
            [
                'diagnostic_id' => $diagnostic->id,
                'diagnosticmodule_id' => $moduleId,
            ],
            [
                'score_total' => $scoreCalcule['score_total'],
                'score_max' => $scoreCalcule['score_max'],
                'score_pourcentage' => $scoreCalcule['score_pourcentage'],
                'diagnosticblocstatut_id' => $this->determinerStatutBloc($scoreCalcule['score_pourcentage']),
            ],
            ['diagnostic_id', 'diagnosticmodule_id'] // Forcer l'update même si existe
        );
        
        \Log::info('Score module créé/mis à jour avec score cumulé', [
            'diagnostic_id' => $diagnostic->id,
            'module_id' => $moduleId,
            'score_total' => $scoreCalcule['score_total'],
            'score_max' => $scoreCalcule['score_max'],
            'score_pourcentage' => $scoreCalcule['score_pourcentage'],
            'module_score_id' => $moduleScore->id,
            'was_created' => $moduleScore->wasRecentlyCreated
        ]);

        // Récupérer tous les modules pour trouver le suivant
        $allModules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->where('etat', 1)
            ->orderBy('position')
            ->get();
        
        $currentModuleIndex = $allModules->search(function($module) use ($moduleId) {
            return $module->id == $moduleId;
        });
        
        $nextModule = $allModules->get($currentModuleIndex + 1);

        // Rediriger vers le module suivant ou rester sur le dernier
        if ($nextModule) {
            $moduleActuel = $currentModuleIndex + 1;
            $totalModules = $allModules->count();
            return redirect()->route('diagnostic.showModule', $nextModule->id)
                ->with('success', "✅ Module {$moduleActuel}/{$totalModules} enregistré avec succès ! Continuez sur le module suivant.");
        } else {
            return redirect()->back()
                ->with('success', '✅ Dernier module enregistré ! Vous pouvez maintenant finaliser le diagnostic.')
                ->with('showFinalization', true);
        }
    }

    /**
     * Calculer le score total cumulé d'un module à partir des réponses
     */
    private function calculerScoreTotalModule($diagnosticId, $moduleId)
    {
        // Récupérer toutes les réponses de l'utilisateur pour ce module
        $reponses = Diagnosticresultat::where('diagnostic_id', $diagnosticId)
            ->whereHas('diagnosticquestion', function($query) use ($moduleId) {
                $query->where('diagnosticmodule_id', $moduleId);
            })
            ->with(['diagnosticreponse', 'diagnosticquestion'])
            ->get();

        $scoreTotal = 0;
        $scoreMax = 0;

        foreach ($reponses as $reponse) {
            // Ajouter les points de la réponse choisie
            if ($reponse->diagnosticreponse) {
                $scoreTotal += $reponse->diagnosticreponse->score ?? 0;
            }
            
            // Calculer le score maximum possible pour cette question
            $pointsMax = $reponse->diagnosticquestion->diagnosticreponses()
                ->max('score') ?? 0;
            $scoreMax += $pointsMax;
        }

        // Calculer le pourcentage
        $scorePourcentage = $scoreMax > 0 ? round(($scoreTotal / $scoreMax) * 100, 2) : 0;

        \Log::info('Score calculé pour module', [
            'diagnostic_id' => $diagnosticId,
            'module_id' => $moduleId,
            'score_total' => $scoreTotal,
            'score_max' => $scoreMax,
            'score_pourcentage' => $scorePourcentage,
            'reponses_count' => $reponses->count()
        ]);

        return [
            'score_total' => $scoreTotal,
            'score_max' => $scoreMax,
            'score_pourcentage' => $scorePourcentage
        ];
    }

    public function store(Request $request, RecompenseService $recompenseService, $moduleId = null)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
    }

    $answers = $request->reponses ?? [];

    // 🔍 Vérifier si au moins une réponse a été fournie
    if (empty($answers) || !is_array($answers)) {
        return redirect()->back()
            ->with('error', '⚠️ Veuillez répondre à au moins une question avant de finaliser.')
            ->withInput();
    }

    // 🔍 Vérifier si les réponses contiennent des valeurs valides
    $hasValidAnswers = false;
    foreach ($answers as $question_id => $values) {
        if (is_array($values)) {
            if (!empty(array_filter($values))) {
                $hasValidAnswers = true;
                break;
            }
        } elseif (!empty($values)) {
            $hasValidAnswers = true;
            break;
        }
    }

    if (!$hasValidAnswers) {
        return redirect()->back()
            ->with('error', '⚠️ Veuillez cocher au moins une réponse avant de finaliser.')
            ->withInput();
    }

    // Récupérer le dernier diagnostic en cours pour ce membre
    $diagnostic = Diagnostic::where('membre_id', $membre->id)
        ->where('entreprise_id', 0)
        ->where('diagnostictype_id', 1) 
        ->where('diagnosticstatut_id', 1) // Non terminé
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$diagnostic) {
        return redirect()->back()->with('error', '⚠️ Aucun diagnostic en cours trouvé.');
    }

    // 🔄 Utiliser une transaction pour la cohérence des données
    \DB::transaction(function () use ($diagnostic, $moduleId, $answers) {
        // Sauvegarder les réponses du dernier module D'ABORD
        if ($moduleId) {
            // Supprimer les anciens résultats pour ce module
            $moduleQuestionIds = Diagnosticmodule::find($moduleId)
                ->diagnosticquestions()
                ->pluck('id')
                ->toArray();
                
            Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->whereIn('diagnosticquestion_id', $moduleQuestionIds)
                ->delete();

            // Enregistrer les nouvelles réponses pour ce module
            foreach ($answers as $questionId => $reponseData) {
                // Si c'est un tableau (checkbox), traiter chaque élément
                if (is_array($reponseData)) {
                    foreach ($reponseData as $reponseId) {
                        Diagnosticresultat::create([
                            'diagnostic_id' => $diagnostic->id,
                            'diagnosticquestion_id' => $questionId,
                            'diagnosticreponse_id' => $reponseId,
                            'etat' => 1,
                        ]);
                    }
                } 
                // Si c'est une valeur simple (radio), traiter directement
                elseif ($reponseData) {
                    Diagnosticresultat::create([
                        'diagnostic_id' => $diagnostic->id,
                        'diagnosticquestion_id' => $questionId,
                        'diagnosticreponse_id' => $reponseData,
                        'etat' => 1,
                    ]);
                }
            }
        }
    });

    // 🔍 Maintenant vérifier toutes les questions obligatoires de tous les modules
    $allModules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
        ->where('etat', 1)
        ->orderBy('position')
        ->with(['diagnosticquestions' => function ($q) {
            $q->where('etat', 1)
              ->where('obligatoire', 1);
        }])
        ->get();
        
    $obligatoires = $allModules
        ->flatMap(fn($module) => $module->diagnosticquestions)
        ->pluck('id')
        ->toArray();

    // 🔍 Vérifier si toutes les questions obligatoires sont répondues
    $repondues = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
        ->whereIn('diagnosticquestion_id', $obligatoires)
        ->distinct()
        ->pluck('diagnosticquestion_id')
        ->toArray();

    $obligatoiresManquantes = array_diff($obligatoires, $repondues);
    if (!empty($obligatoiresManquantes)) {
        $nbManquantes = count($obligatoiresManquantes);
        
        // Récupérer les modules où se trouvent les questions obligatoires manquantes
        $modulesAvecQuestionsManquantes = [];
        foreach ($allModules as $index => $module) {
            $questionsManquantesDansModule = $module->diagnosticquestions
                ->whereIn('id', $obligatoiresManquantes);
                
            if ($questionsManquantesDansModule->isNotEmpty()) {
                $modulesAvecQuestionsManquantes[] = ($index + 1); // +1 pour afficher le numéro réel
            }
        }
        
        $modulesList = implode(', ', $modulesAvecQuestionsManquantes);
        $moduleText = count($modulesAvecQuestionsManquantes) > 1 ? 'modules' : 'module';
        
        return redirect()->back()
            ->with('warning', "⚠️ Il reste {$nbManquantes} question(s) obligatoire(s) non remplie(s) dans le {$moduleText} {$modulesList}. Veuillez compléter avant de finaliser.")
            ->withInput();
    }

    // Calculer le score total
    $totalScore = 0;
    $resultats = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)->get();
    foreach ($resultats as $resultat) {
        $reponse = Diagnosticreponse::find($resultat->diagnosticreponse_id);
        $totalScore += $reponse?->score ?? 0;
    }

    // 💯 Diagnostic terminé
    $diagnostic->update([
        'scoreglobal' => $totalScore,
        'diagnosticstatut_id' => 2, // terminé
    ]);

    // � Créer une évolution pour le diagnostic PME (sans entreprise)
    Diagnosticevolution::creerEvolution(
        0, // Pas d'entreprise_id pour les diagnostics PME
        $diagnostic->id,
        null, // Pas de diagnostic précédent spécifique
        "Diagnostic PME terminé - Score: {$totalScore}"
    );

    // �🏁 Création automatique d'un accompagnement
    $accompagnement = Accompagnement::create([
        'membre_id' => $membre->id,
        'accompagnementniveau_id' => 1,
        'dateaccompagnement' => now(),
        'accompagnementstatut_id' => 1,
    ]);

    // 🔗 Lier le diagnostic à l'accompagnement
    $diagnostic->update([
        'accompagnement_id' => $accompagnement->id,
    ]);

    // 🎯 GÉNÉRATION AUTOMATIQUE DES PLANS D'ACCOMPAGNEMENT
    $this->genererPlansAutomatiques($diagnostic);

    // 🏆 Vérifie si c'est le premier diagnostic PME du membre
    $nbDiagnostics = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', 0)
        ->where('diagnosticstatut_id', 2)
        ->count();

    // 🏁 Déclenche la récompense "DIAG_PME_PREMIER"
    if ($nbDiagnostics == 1) {
        $recompense = $recompenseService->attribuerRecompense('DIAG_PME_PREMIER', $membre, null, $diagnostic->id);
    }

    // 🔧 Redirection directe pour éviter les problèmes
    return redirect("/diagnostics/diagnostic/success/{$diagnostic->id}")
        ->with('success', 'Diagnostic terminé avec succès. Score : ' . $totalScore)
        ->with('diagnostic_id', $diagnostic->id);
}

    /**
     * Détermine le statut du bloc en fonction du pourcentage de score
     * Basé sur la table diagnosticblocstatuts :
     * 1 = critique (Bloc bloquant nécessitant un accompagnement prioritaire)
     * 2 = fragile (Bloc insuffisamment structuré)
     * 3 = intermediaire (Bloc partiellement structuré)
     * 4 = conforme (Bloc conforme aux attentes du palier)
     * 5 = reference (Bloc exemplaire – niveau référence)
     */
    private function determinerStatutBloc($scorePourcentage)
    {
        // Logique de détermination du statut basé sur le pourcentage
        if ($scorePourcentage >= 90) {
            return 5; // reference - Bloc exemplaire
        } elseif ($scorePourcentage >= 75) {
            return 4; // conforme - Bloc conforme aux attentes
        } elseif ($scorePourcentage >= 50) {
            return 3; // intermediaire - Bloc partiellement structuré
        } elseif ($scorePourcentage >= 25) {
            return 2; // fragile - Bloc insuffisamment structuré
        } else {
            return 1; // critique - Bloc bloquant
        }
    }

    /**
     * Affiche la page de succès avec les détails du diagnostic
     */
    public function success($diagnosticId)
    {
        // DEBUG: Vérifier l'appel à la méthode success
        $debugSuccess = [
            'diagnosticId_received' => $diagnosticId,
            'method_called' => 'success',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];
        session(['debug_success_data' => $debugSuccess]);
        
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        // Récupérer le diagnostic avec toutes ses relations
        $diagnostic = Diagnostic::where('id', $diagnosticId)
            ->where('diagnostictype_id', 1) // diagnostic PME
            ->with([
                'entreprise',
                'accompagnement',
                'diagnosticresultats.diagnosticquestion.diagnosticmodule',
                'diagnosticresultats.diagnosticreponse',
                'diagnosticmodulescores.diagnosticmodule'
            ])
            ->firstOrFail();

        // Vérifier que le diagnostic appartient au membre
        if ($diagnostic->membre_id != $membre->id) {
            return redirect()->route('diagnostic.form')
                ->with('error', 'Accès non autorisé à ce diagnostic.');
        }

        // Récupérer tous les modules pour l'affichage (type 1 pour PME)
        $modules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->where('etat', 1)
            ->orderBy('position')
            ->with(['diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                  ->orderBy('position')
                  ->with(['diagnosticreponses' => fn($query) => $query->where('etat', 1)]);
            }])
            ->get();

        return view('diagnostic.success', compact('diagnostic', 'modules'));
    }

    /**
     * Affiche la liste des plans d'accompagnement pour un diagnostic
     */
    public function listePlans($diagnosticId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Vérifier que le diagnostic appartient au membre
        $diagnostic = Diagnostic::where('id', $diagnosticId)
            ->where('membre_id', $membre->id)
            ->with(['accompagnement.plans', 'diagnosticmodulescores.diagnosticmodule'])
            ->firstOrFail();

        return view('diagnostic.plans', compact('diagnostic'));
    }

/**
 * Convertit un score de réponse (1-4) en niveau (A-D)
 * Note: D est la valeur maximale dans notre système
 */
public function convertirScoreEnNiveau($score)
{
    $conversion = [
        1 => 'A', // Faible
        2 => 'B', // Moyen
        3 => 'C', // Bon
        4 => 'D', // Excellent (maximal)
    ];
    
    return $conversion[$score] ?? 'A';
}

/**
 * Calcule le niveau moyen pour un module basé sur toutes les réponses
 */
private function calculerNiveauModule($diagnosticId, $moduleId)
{
    // Récupérer toutes les réponses pour ce module
    $reponses = Diagnosticresultat::where('diagnostic_id', $diagnosticId)
        ->whereHas('diagnosticquestion', function($q) use ($moduleId) {
            $q->where('diagnosticmodule_id', $moduleId);
        })
        ->with('diagnosticreponse')
        ->get();

    if ($reponses->isEmpty()) {
        return 'A'; // Niveau par défaut
    }

    // Calculer le score moyen
    $scoreTotal = $reponses->sum(function($reponse) {
        return $reponse->diagnosticreponse->score ?? 0;
    });
    
    $scoreMoyen = $scoreTotal / $reponses->count();
    
    // Convertir en niveau
    return $this->convertirScoreEnNiveau(round($scoreMoyen));
}

/**
 * Génère automatiquement les plans d'action basés sur les scores du diagnostic
 */
private function genererPlansAutomatiques($diagnostic)
{
    try {
        \Log::info('Début génération automatique des plans', [
            'diagnostic_id' => $diagnostic->id,
            'accompagnement_id' => $diagnostic->accompagnement_id
        ]);
        
        // Récupérer l'accompagnement
        $accompagnement = Accompagnement::find($diagnostic->accompagnement_id);
        
        if (!$accompagnement) {
            \Log::warning('Aucun accompagnement trouvé pour le diagnostic ' . $diagnostic->id);
            return;
        }

        \Log::info('Accompagnement trouvé', [
            'accompagnement_id' => $accompagnement->id,
            'membre_id' => $accompagnement->membre_id,
            'entreprise_id' => $accompagnement->entreprise_id
        ]);

        // Récupérer tous les modules du diagnostic (type 1 pour PME)
        $modules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->whereHas('diagnosticquestions', function($q) use ($diagnostic) {
                $q->whereHas('diagnosticresultats', function($subQ) use ($diagnostic) {
                    $subQ->where('diagnostic_id', $diagnostic->id);
                });
            })
            ->get();

        \Log::info('Modules trouvés', [
            'count' => $modules->count(),
            'modules' => $modules->pluck('id')->toArray(),
            'modules_with_titres' => $modules->pluck('titre', 'id')->toArray()
        ]);

        $plansCrees = 0;
        
        foreach ($modules as $module) {
            // Calculer le score total cumulé du module
            $scoreCalcule = $this->calculerScoreTotalModule($diagnostic->id, $module->id);
            
            \Log::info('Score calculé pour module', [
                'module_id' => $module->id,
                'module_titre' => $module->titre,
                'score_total' => $scoreCalcule['score_total'],
                'score_max' => $scoreCalcule['score_max'],
                'score_pourcentage' => $scoreCalcule['score_pourcentage'],
                'niveau_calcule' => $this->convertirScoreEnNiveau($scoreCalcule['score_total'])
            ]);
            
            // Créer ou mettre à jour le score du module avec le score cumulé
            $moduleScore = Diagnosticmodulescore::updateOrCreate(
                [
                    'diagnostic_id' => $diagnostic->id,
                    'diagnosticmodule_id' => $module->id,
                ],
                [
                    'score_total' => $scoreCalcule['score_total'],
                    'score_max' => $scoreCalcule['score_max'],
                    'score_pourcentage' => $scoreCalcule['score_pourcentage'],
                    'diagnosticblocstatut_id' => $this->determinerStatutBloc($scoreCalcule['score_pourcentage']),
                ],
                ['diagnostic_id', 'diagnosticmodule_id'] // Forcer l'update même si existe
            );
            
            \Log::info('Score module créé/mis à jour', [
                'diagnostic_id' => $diagnostic->id,
                'module_id' => $module->id,
                'score_total' => $scoreCalcule['score_total'],
                'score_max' => $scoreCalcule['score_max'],
                'score_pourcentage' => $scoreCalcule['score_pourcentage'],
                'module_score_id' => $moduleScore->id,
                'was_created' => $moduleScore->wasRecentlyCreated
            ]);

            // Chercher les templates correspondants
            $templates = Plantemplate::where('diagnosticmodule_id', $module->id)
                ->where('niveau', $this->convertirScoreEnNiveau($scoreCalcule['score_total']))
                ->actif()
                ->get();

            \Log::info('Templates trouvés', [
                'module_id' => $module->id,
                'niveau_calcule' => $this->convertirScoreEnNiveau($scoreCalcule['score_total']),
                'templates_count' => $templates->count()
            ]);

            foreach ($templates as $template) {
                \Log::info('Traitement template', [
                    'template_id' => $template->id,
                    'priorite' => $template->priorite,
                    'priorite_type' => gettype($template->priorite)
                ]);
                // Vérifier si le plan n'existe pas déjà
                $planExistant = Plan::where('accompagnement_id', $accompagnement->id)
                    ->where('objectif', $template->objectif)
                    ->where('actionprioritaire', $template->actionprioritaire)
                    ->exists();

                if (!$planExistant) {
                    $plan = Plan::create([
                        'objectif' => $template->objectif,
                        'actionprioritaire' => $template->actionprioritaire,
                        'dateplan' => now()->addDays((int)$template->priorite * 7), // Priorité = délai en semaines
                        'accompagnement_id' => $accompagnement->id,
                        'etat' => 1,
                        'spotlight' => 0,
                    ]);
                    
                    \Log::info('Plan créé', [
                        'plan_id' => $plan->id,
                        'template_id' => $template->id,
                        'objectif' => $template->objectif
                    ]);
                    
                    $plansCrees++;
                } else {
                    \Log::info('Plan déjà existant', [
                        'template_id' => $template->id,
                        'objectif' => $template->objectif
                    ]);
                }
            }
        }

        \Log::info("Génération automatique terminée : {$plansCrees} plans créés pour le diagnostic {$diagnostic->id}");
        
    } catch (\Exception $e) {
        \Log::error("Erreur lors de la génération automatique des plans : " . $e->getMessage(), [
            'diagnostic_id' => $diagnostic->id ?? 'unknown',
            'trace' => $e->getTraceAsString()
        ]);
        // Ne pas bloquer le processus de diagnostic
    }
}

/**
 * Convertit un niveau (A-D) en pourcentage
 * Note: D est la valeur maximale (90%)
 */
private function convertirNiveauEnPourcentage($niveau)
{
    $conversion = [
        'A' => 25, // Faible
        'B' => 50, // Moyen
        'C' => 75, // Bon
        'D' => 90, // Excellent (maximal)
    ];
    
    return $conversion[$niveau] ?? 25;
}

/**
 * Convertit un niveau (A-D) en score numérique
 * Note: D est la valeur maximale (90)
 */
public static function convertirNiveauEnScore($niveau)
{
    $conversion = [
        'A' => 25, // Faible
        'B' => 50, // Moyen
        'C' => 75, // Bon
        'D' => 90, // Excellent (maximal)
    ];
    
    return $conversion[$niveau] ?? 25;
}

/**
 * Affiche tous les diagnostics du membre connecté
 */
public function mesDiagnostics()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->firstOrFail();
    
    // Récupérer tous les diagnostics du membre
    $diagnostics = Diagnostic::where('membre_id', $membre->id)
        ->with(['diagnosticstatut', 'diagnostictype', 'diagnosticmodulescores' => function($query) {
            $query->with(['diagnosticmodule', 'diagnosticblocstatut']);
        }])
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('diagnostic.mes-diagnostics', compact('diagnostics', 'membre'));
}

}
