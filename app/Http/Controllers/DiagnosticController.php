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

use App\Services\RecompenseService;
use App\Services\DiagnosticStatutService;

class DiagnosticController extends Controller
{
    protected $diagnosticStatutService;

    public function __construct(DiagnosticStatutService $diagnosticStatutService)
    {
        $this->diagnosticStatutService = $diagnosticStatutService;
    }

    public function selectCategory()
    {
        $userId = Auth::id();
        
        // Vérification du membre connecté
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Récupérer toutes les catégories de modules disponibles (nouveau système)
        $categories = \App\Models\Diagnosticmodulecategory::where('etat', 1)
            ->withCount(['diagnosticmodules' => function($query) {
                $query->where('etat', 1);
            }])
            ->orderBy('id')
            ->get();

        return view('diagnostic.select-category', compact('categories', 'membre'));
    }

    public function selectType()
    {
        $userId = Auth::id();
        
        // Vérification du membre connecté
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Récupérer tous les types de modules disponibles (ancien système)
        $types = \App\Models\Diagnosticmoduletype::where('etat', 1)
            ->withCount(['diagnosticmodules' => function($query) {
                $query->where('etat', 1);
            }])
            ->orderBy('id')
            ->get();

        return view('diagnostic.select-type', compact('types', 'membre'));
    }

    public function showFormByCategory($categoryId = null, $moduleId = null)
    {
        $userId = Auth::id();
        
        // Vérification du membre connecté
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Si aucune catégorie n'est spécifiée, rediriger vers la sélection
        if (!$categoryId) {
            return redirect()->route('diagnostic.select.category');
        }

        // Vérifier que la catégorie existe
        $category = \App\Models\Diagnosticmodulecategory::where('etat', 1)->find($categoryId);
        if (!$category) {
            return redirect()->route('diagnostic.select.category')
                ->with('error', '⚠️ Catégorie de modules non trouvée.');
        }

    // Récupération des modules de la catégorie spécifiée, triés par position
    $allDiagnosticmodules = $this->getModulesForCategory($categoryId, 1);

    // Si aucun moduleId spécifié, prendre le premier
    if ($moduleId === null) {
        $currentModule = $allDiagnosticmodules->first();
        $moduleId = $currentModule ? $currentModule->id : null;
    } else {
        $currentModule = $allDiagnosticmodules->where('id', $moduleId)->first();
    }

    // Récupérer tous les modules pour la navigation
    $modules = $allDiagnosticmodules;
    
    $currentModuleIndex = 0;
    if ($currentModule) {
        // Approche ultra-simple
        $moduleIds = $allDiagnosticmodules->pluck('id')->toArray();
        $targetId = (int)$currentModule->id;
        
        foreach ($moduleIds as $index => $moduleId_search) {
            if ((int)$moduleId_search === $targetId) {
                $currentModuleIndex = $index;
                break;
            }
        }
    }
    
    $nextModule = $currentModule ? $allDiagnosticmodules->get($currentModuleIndex + 1) : null;
    $previousModule = $currentModuleIndex > 0 ? $allDiagnosticmodules->get($currentModuleIndex - 1) : null;
    $isLastModule = $currentModule ? ($currentModuleIndex + 1) >= $allDiagnosticmodules->count() : false;
    
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
        'membre',
        'category'
    ));
    }

    /**
     * Récupérer les modules pour une catégorie et un type donnés
     */
    private function getModulesForCategory($categoryId, $typeId = 1)
    {
        return Diagnosticmodule::where('diagnosticmoduletype_id', $typeId)
            ->where('diagnosticmodulecategory_id', $categoryId)
            ->where('etat', 1)
            ->orderByRaw('CAST(position AS UNSIGNED)') // Tri numérique sur position
            ->with(['diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                  ->orderByRaw('CAST(position AS UNSIGNED)') // Cast en nombre pour tri numérique
                  ->with(['diagnosticreponses' => function ($query) {
                      $query->where('etat', 1)
                            ->inRandomOrder(); // mélange aléatoire des réponses
                  }]);
            }])
            ->get();
    }

    public function showForm($categoryId = null, $moduleId = null)
    {
        // Nettoyer la session showFinalization pour éviter les messages persistants
        session()->forget('showFinalization');
        
        $userId = Auth::id();
        
        // Vérification du membre connecté
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Si aucune catégorie n'est spécifiée, rediriger vers la sélection
        if (!$categoryId) {
            return redirect()->route('diagnostic.select.category');
        }

        // Vérifier que la catégorie existe
        $category = \App\Models\Diagnosticmodulecategory::where('etat', 1)->find($categoryId);
        if (!$category) {
            return redirect()->route('diagnostic.select.category')
                ->with('error', '⚠️ Catégorie de modules non trouvée.');
        }

    // Récupération des modules du type 1 (PME) ET de la catégorie spécifiée, triés par position
    $allDiagnosticmodules = $this->getModulesForCategory($categoryId, 1);

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
        'membre',
        'category'
    ));
}




    public function saveModule(Request $request, $categoryId, $moduleId)
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
                ->where('diagnosticmodulecategory_id', $categoryId) // Filtrer par catégorie
                ->where('etat', 1)
                ->orderByRaw('CAST(position AS UNSIGNED)') // Tri numérique sur position
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

        // Calculer et enregistrer les scores par question pour ce module
        $this->creerScoresParQuestionFromModule($diagnostic->id, $moduleId);

        // Récupérer tous les modules de la catégorie pour trouver le suivant
        $allModules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->where('diagnosticmodulecategory_id', $categoryId) // Filtrer par catégorie
            ->where('etat', 1)
            ->orderByRaw('CAST(position AS UNSIGNED)') // Tri numérique sur position
            ->get();
        
        $currentModuleIndex = $allModules->search(function($module) use ($moduleId) {
            return $module->id == $moduleId;
        });
        
        $nextModule = $allModules->get($currentModuleIndex + 1);

        // Rediriger vers le module suivant ou rester sur le dernier
        if ($nextModule) {
            $moduleActuel = $currentModuleIndex + 1;
            $totalModules = $allModules->count();
            return redirect()->route('diagnostic.showModule', [$categoryId, $nextModule->id])
                ->with('success', "✅ Module {$moduleActuel}/{$totalModules} enregistré avec succès ! Continuez sur le module suivant.");
        } else {
            // Définir la session pour la finalisation
            session(['showFinalization' => true]);
            return redirect()->back()
                ->with('success', '✅ Dernier module enregistré ! Vous pouvez maintenant finaliser le diagnostic.');
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

        
        // Créer les scores par question pour ce module
        $this->creerScoresParQuestion($diagnosticId, $moduleId, $reponses);

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

    /**
     * Créer les scores par question pour un module
     */
    private function creerScoresParQuestion($diagnosticId, $moduleId, $reponses)
    {
        foreach ($reponses as $reponse) {
            $questionId = $reponse->diagnosticquestion_id;
            $scoreObtenu = $reponse->diagnosticreponse->score ?? 0;
            
            // Récupérer le score maximum possible pour cette question
            $scoreMax = Diagnosticreponse::where('diagnosticquestion_id', $questionId)
                ->max('score') ?? 0;
            
            $pourcentage = $scoreMax > 0 ? round(($scoreObtenu / $scoreMax) * 100, 2) : 0;
            
            // Créer ou mettre à jour le score de la question
            Diagnosticmodulescore::updateOrCreate(
                [
                    'diagnostic_id' => $diagnosticId,
                    'diagnosticmodule_id' => $moduleId,
                    'diagnosticquestion_id' => $questionId,
                ],
                [
                    'score_total' => $scoreObtenu,
                    'score_max' => $scoreMax,
                    'score_pourcentage' => $pourcentage,
                    'diagnosticblocstatut_id' => $this->determinerStatutBloc($pourcentage),
                ],
                ['diagnostic_id', 'diagnosticmodule_id', 'diagnosticquestion_id']
            );
        }
    }

    /**
     * Créer les scores par question pour un module (version pour saveModule)
     */
    private function creerScoresParQuestionFromModule($diagnosticId, $moduleId)
    {
        // Récupérer toutes les réponses de l'utilisateur pour ce module
        $reponses = Diagnosticresultat::where('diagnostic_id', $diagnosticId)
            ->whereHas('diagnosticquestion', function($query) use ($moduleId) {
                $query->where('diagnosticmodule_id', $moduleId);
            })
            ->with(['diagnosticreponse', 'diagnosticquestion'])
            ->get();

        foreach ($reponses as $reponse) {
            $questionId = $reponse->diagnosticquestion_id;
            $scoreObtenu = $reponse->diagnosticreponse->score ?? 0;
            
            // Récupérer le score maximum possible pour cette question
            $scoreMax = Diagnosticreponse::where('diagnosticquestion_id', $questionId)
                ->max('score') ?? 0;
            
            $pourcentage = $scoreMax > 0 ? round(($scoreObtenu / $scoreMax) * 100, 2) : 0;
            
            // Créer ou mettre à jour le score de la question
            Diagnosticmodulescore::updateOrCreate(
                [
                    'diagnostic_id' => $diagnosticId,
                    'diagnosticmodule_id' => $moduleId,
                    'diagnosticquestion_id' => $questionId,
                ],
                [
                    'score_total' => $scoreObtenu,
                    'score_max' => $scoreMax,
                    'score_pourcentage' => $pourcentage,
                    'diagnosticblocstatut_id' => $this->determinerStatutBloc($pourcentage),
                ],
                ['diagnostic_id', 'diagnosticmodule_id', 'diagnosticquestion_id']
            );
        }

        \Log::info('Scores par question créés pour module', [
            'diagnostic_id' => $diagnosticId,
            'module_id' => $moduleId,
            'reponses_count' => $reponses->count()
        ]);
    }

    /**
     * Finalise le diagnostic (appelé uniquement pour le dernier module)
     */
    public function finalizeModule(Request $request, RecompenseService $recompenseService, $categoryId, $moduleId)
    {
        // Appeler la méthode store qui finalise tout le diagnostic
        return $this->store($request, $recompenseService, $categoryId, $moduleId);
    }

    public function store(Request $request, RecompenseService $recompenseService, $categoryId, $moduleId = null)
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

    // 🔍 Maintenant vérifier toutes les questions obligatoires de tous les modules de la catégorie
    $allModules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
        ->where('diagnosticmodulecategory_id', $categoryId) // Filtrer par catégorie
        ->where('etat', 1)
        ->orderByRaw('CAST(position AS UNSIGNED)') // Tri numérique sur position
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

    // 💾 Créer les scores par module (essentiel pour l'affichage)
    \Log::info('Début création scores modules', ['diagnostic_id' => $diagnostic->id]);
    $this->creerScoresModules($diagnostic);
    \Log::info('Scores modules créés');

    // 🎯 Finaliser le diagnostic et récupérer le score total
    \Log::info('Début finalisation diagnostic', ['diagnostic_id' => $diagnostic->id]);
    $totalScore = $this->processDiagnosticFinalization($diagnostic, $membre, $recompenseService);
    \Log::info('Finalisation terminée', ['total_score' => $totalScore]);

    // 🔧 Redirection directe pour éviter les problèmes
    \Log::info('Redirection vers success', ['diagnostic_id' => $diagnostic->id]);
    
    // Nettoyer la session de finalisation pour éviter le message persistant
    session()->forget('showFinalization');
    
    return redirect("/diagnostics/diagnostic/success/{$diagnostic->id}")
        ->with('success', 'Diagnostic terminé avec succès. Score : ' . $totalScore)
        ->with('diagnostic_id', $diagnostic->id);
}
    /**
     * Créer les scores par module pour un diagnostic
     */
    private function creerScoresModules($diagnostic)
    {
        // Récupérer tous les modules de type 1 (PME) de la catégorie du diagnostic
        $categoryId = $diagnostic->categoryId ?? null; // Si le categoryId est stocké dans le diagnostic
        
        $query = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->where('etat', 1);
            
        if ($categoryId) {
            $query->where('diagnosticmodulecategory_id', $categoryId);
        }
        
        $modules = $query->with(['diagnosticquestions' => function ($q) {
            $q->where('etat', 1);
        }])->get();

        foreach ($modules as $module) {
            // Récupérer les réponses pour ce module
            $reponsesModule = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->whereHas('diagnosticquestion', function($q) use ($module) {
                    $q->where('diagnosticmodule_id', $module->id);
                })
                ->with('diagnosticreponse')
                ->get();

            if ($reponsesModule->isNotEmpty()) {
                // Calculer le score total pour ce module
                $scoreTotal = $reponsesModule->sum(function($reponse) {
                    return $reponse->diagnosticreponse->score ?? 0;
                });

                // Calculer le score maximum possible (2 points par question)
                $scoreMax = $module->diagnosticquestions->count() * 2;
                $scorePourcentage = $scoreMax > 0 ? ($scoreTotal / $scoreMax) * 100 : 0;

                // Déterminer le statut du bloc
                $statutId = $this->determinerStatutBloc($scorePourcentage);

                // Supprimer l'ancien score s'il existe
                Diagnosticmodulescore::where('diagnostic_id', $diagnostic->id)
                    ->where('diagnosticmodule_id', $module->id)
                    ->delete();

                // Créer le nouveau score de module
                Diagnosticmodulescore::create([
                    'diagnostic_id' => $diagnostic->id,
                    'diagnosticmodule_id' => $module->id,
                    'diagnosticblocstatut_id' => $statutId,
                    'score_total' => $scoreTotal,
                    'score_max' => $scoreMax,
                    'score_pourcentage' => $scorePourcentage,
                    'etat' => 1,
                ]);
            }
        }
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
        if ($scorePourcentage < 40) {
            return 1; // critique
        } elseif ($scorePourcentage < 60) {
            return 2; // fragile
        } elseif ($scorePourcentage < 80) {
            return 3; // intermediaire
        } elseif ($scorePourcentage < 100) {
            return 4; // conforme
        } else {
            return 5; // reference
        }
    }

    /**
     * Traite la finalisation d'un diagnostic : scores, accompagnement, plans, récompenses
     */
    private function processDiagnosticFinalization($diagnostic, $membre, $recompenseService)
    {
        \Log::info('Début processDiagnosticFinalization', ['diagnostic_id' => $diagnostic->id]);
        
        // Calculer le score total
        $totalScore = 0;
        $resultats = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)->get();
        \Log::info('Résultats récupérés', ['count' => $resultats->count()]);
        
        foreach ($resultats as $resultat) {
            $reponse = Diagnosticreponse::find($resultat->diagnosticreponse_id);
            $totalScore += $reponse?->score ?? 0;
        }
        \Log::info('Score total calculé', ['total_score' => $totalScore]);

        // 💯 Diagnostic terminé
        \Log::info('Mise à jour diagnostic statut');
        $diagnostic->update([
            'scoreglobal' => $totalScore,
            'diagnosticstatut_id' => 2, // terminé
        ]);

        // 🏁 Création automatique d'un accompagnement
        \Log::info('Création accompagnement');
        $accompagnement = Accompagnement::create([
            'membre_id' => $membre->id,
            'entreprise_id' => 0,
            'diagnostic_id' => $diagnostic->id,
            'accompagnementniveau_id' => 1,
            'dateaccompagnement' => now(),
            'accompagnementstatut_id' => 1,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        // 🎯 GÉNÉRATION AUTOMATIQUE DES PLANS D'ACCOMPAGNEMENT
        \Log::info('Début génération plans automatiques');
        $this->genererPlansAutomatiques($diagnostic);
        \Log::info('Génération plans automatiques terminée');

        // 🏆 Vérifie si c'est le premier diagnostic PME du membre
        \Log::info('Vérification premier diagnostic');
        $nbDiagnostics = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', 0)
            ->where('diagnosticstatut_id', 2)
            ->count();

        // 🏁 Déclenche la récompense "DIAG_PME_PREMIER"
        if ($nbDiagnostics == 1) {
            \Log::info('Attribution récompense premier diagnostic');
            $recompense = $recompenseService->attribuerRecompense('DIAG_PME_PREMIER', $membre, null, $diagnostic->id);
        }
        
        \Log::info('Fin processDiagnosticFinalization', ['total_score' => $totalScore]);
        return $totalScore;
    }

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
            return redirect()->route('diagnostic.select.category')
                ->with('error', 'Accès non autorisé à ce diagnostic.');
        }

        // Récupérer tous les modules pour l'affichage (type 1 pour PME) de la catégorie du diagnostic
        // Récupérer la catégorie à partir des modules répondus dans ce diagnostic
        $categoryId = null;
        $firstResult = $diagnostic->diagnosticresultats->first();
        if ($firstResult && $firstResult->diagnosticquestion && $firstResult->diagnosticquestion->diagnosticmodule) {
            $categoryId = $firstResult->diagnosticquestion->diagnosticmodule->diagnosticmodulecategory_id;
        }
        
        \Log::info('Catégorie récupérée depuis les résultats', [
            'diagnostic_id' => $diagnostic->id,
            'category_id' => $categoryId,
            'first_result_id' => $firstResult?->id
        ]);
        
        $query = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->where('etat', 1);
            
        if ($categoryId) {
            $query->where('diagnosticmodulecategory_id', $categoryId);
        }
        
        $modules = $query->with(['diagnosticmodulecategory', 'diagnosticquestions' => function ($q) {
            $q->where('etat', 1)
              ->orderByRaw('CAST(position AS UNSIGNED)') // Tri numérique sur position
              ->with(['diagnosticreponses' => fn($query) => $query->where('etat', 1)]);
        }])
        ->orderByRaw('CAST(position AS UNSIGNED)') // Tri numérique sur position
        ->get();

        // Calculer les niveaux par module
        $niveauxModules = [];
        foreach ($modules as $module) {
            $niveauxModules[$module->id] = $this->calculerNiveauMoyenModule($diagnostic->id, $module->id);
        }

        // Récupérer les blocs critiques pour l'affichage
        $blocsCritiques = $diagnostic->diagnosticmodulescores()
            ->with(['diagnosticmodule', 'diagnosticblocstatut'])
            ->get()
            ->map(function($moduleScore) {
                // Récupérer les orientations pour ce module selon son score
                $orientations = \App\Models\Diagnosticorientation::getOrientationsPourModule(
                    $moduleScore->diagnosticmodule_id, 
                    $moduleScore->score_total ?? 0
                );
                
                return [
                    'nom' => $moduleScore->diagnosticmodule->titre ?? $moduleScore->diagnosticmodule->code,
                    'score' => $moduleScore->score_total ?? 0,
                    'pourcentage' => $moduleScore->score_pourcentage ?? 0,
                    'statut' => $moduleScore->diagnosticblocstatut->titre ?? 'Non défini',
                    'module_id' => $moduleScore->diagnosticmodule_id,
                    'orientations' => $orientations
                ];
            })
            ->toArray();

        return view('diagnostic.success', compact('diagnostic', 'modules', 'niveauxModules', 'blocsCritiques'));
    }

    /**
     * Affiche la liste des plans d'accompagnement pour un diagnostic
     */
    public function listePlans($diagnosticId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Récupérer le diagnostic avec toutes ses relations (comme dans success)
        $diagnostic = Diagnostic::where('id', $diagnosticId)
            ->where('diagnostictype_id', 1) // diagnostic PME
            ->where('membre_id', $membre->id)
            ->with([
                'accompagnement.plans.diagnosticmodule',
                'accompagnement.plans.diagnosticquestion',
                'diagnosticresultats.diagnosticquestion.diagnosticmodule',
                'diagnosticresultats.diagnosticreponse',
                'diagnosticmodulescores.diagnosticmodule'
            ])
            ->firstOrFail();

        // Calculer les niveaux par module pour l'affichage (garder pour compatibilité)
        $niveauxModules = [];
        if ($diagnostic->diagnosticmodulescores->isNotEmpty()) {
            $modulesIds = $diagnostic->diagnosticmodulescores->pluck('diagnosticmodule_id')->unique();
            foreach ($modulesIds as $moduleId) {
                $niveauxModules[$moduleId] = $this->calculerNiveauMoyenModule($diagnostic->id, $moduleId);
            }
        }

        // La vue plans.blade.php calcule maintenant les scores directement depuis diagnosticresultats
        // comme dans success.blade.php, donc on n'a plus besoin de préparer $scoresParModule ici
        // La vue fera le calcul elle-même

        return view('diagnostic.plans', compact('diagnostic', 'niveauxModules'));
    }

/**
 * Convertit un score de réponse (1-4) en niveau numérique (1-4)
 * Note: 4 est la valeur maximale dans notre système
 */
public function convertirScoreEnNiveau($score)
{
    // Normaliser le score entre 1 et 4
    if ($score <= 1) {
        return 1; // Faible
    } elseif ($score <= 2) {
        return 2; // Moyen
    } elseif ($score <= 3) {
        return 3; // Bon
    } else {
        return 4; // Excellent (maximal)
    }
}

/**
 * Calcule le niveau moyen pour un module basé sur toutes les questions
 */
private function calculerNiveauMoyenModule($diagnosticId, $moduleId)
{
    // Récupérer tous les scores par question pour ce module
    $scoresQuestions = Diagnosticmodulescore::where('diagnostic_id', $diagnosticId)
        ->where('diagnosticmodule_id', $moduleId)
        ->whereNotNull('diagnosticquestion_id')
        ->get();

    if ($scoresQuestions->isEmpty()) {
        return 'A'; // Niveau par défaut
    }

    // Calculer le score moyen pondéré
    $scoreTotal = $scoresQuestions->sum('score_total');
    $scoreMaxTotal = $scoresQuestions->sum('score_max');
    
    $scoreMoyen = $scoreMaxTotal > 0 ? ($scoreTotal / $scoreMaxTotal) * 4 : 1; // Normaliser sur 1-4
    
    // Convertir en niveau
    return $this->convertirScoreEnNiveau(round($scoreMoyen));
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
            'accompagnement_id' => $diagnostic->accompagnement ? $diagnostic->accompagnement->id : null
        ]);
        
        // Récupérer l'accompagnement
        $accompagnement = $diagnostic->accompagnement;
        
        if (!$accompagnement) {
            \Log::warning('Aucun accompagnement trouvé pour le diagnostic ' . $diagnostic->id);
            return;
        }

        \Log::info('Accompagnement trouvé', [
            'accompagnement_id' => $accompagnement->id,
            'diagnostic_id' => $diagnostic->id
        ]);
        
        // Récupérer tous les modules de la catégorie du diagnostic
        $categoryId = $diagnostic->categoryId ?? null;
        
        $query = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->where('etat', 1);
            
        if ($categoryId) {
            $query->where('diagnosticmodulecategory_id', $categoryId);
        }
        
        $modules = $query->with(['diagnosticquestions' => function($q) use ($diagnostic) {
            $q->whereHas('diagnosticresultats', function($subQ) use ($diagnostic) {
                $subQ->where('diagnostic_id', $diagnostic->id);
            });
        }])
        ->with(['diagnosticresultats' => function($q) use ($diagnostic) {
            $q->where('diagnostic_id', $diagnostic->id)
              ->with('diagnosticreponse');
        }])
        ->get();

        $plansCrees = 0;

        foreach ($modules as $module) {
            // Récupérer les questions pour ce module
            $questionsDuModule = $module->diagnosticquestions()->whereHas('diagnosticresultats', function($q) use ($diagnostic) {
                $q->where('diagnostic_id', $diagnostic->id);
            })
                ->with(['diagnosticresultats' => function($q) use ($diagnostic) {
                    $q->where('diagnostic_id', $diagnostic->id)
                      ->with('diagnosticreponse');
                }])
                ->get();

            \Log::info('Questions trouvées pour module', [
                'module_id' => $module->id,
                'module_titre' => $module->titre,
                'questions_count' => $questionsDuModule->count()
            ]);

            foreach ($questionsDuModule as $question) {
                // Calculer le score pour cette question spécifique
                $reponse = $question->diagnosticresultats->first();
                if ($reponse && $reponse->diagnosticreponse) {
                    $scoreObtenu = $reponse->diagnosticreponse->score ?? 0;
                    $scoreMax = Diagnosticreponse::where('diagnosticquestion_id', $question->id)
                        ->max('score') ?? 0;
                    $pourcentage = $scoreMax > 0 ? round(($scoreObtenu / $scoreMax) * 100, 2) : 0;
                    
                    // Calculer le niveau pour cette question
                    $niveauQuestion = $this->convertirScoreEnNiveau($scoreObtenu);
                    
                    \Log::info('Score calculé pour question', [
                        'question_id' => $question->id,
                        'score_obtenu' => $scoreObtenu,
                        'score_max' => $scoreMax,
                        'pourcentage' => $pourcentage,
                        'niveau_calcule' => $niveauQuestion
                    ]);

                    // Créer ou mettre à jour le score de la question
                    $moduleScore = Diagnosticmodulescore::updateOrCreate(
                        [
                            'diagnostic_id' => $diagnostic->id,
                            'diagnosticmodule_id' => $module->id,
                            'diagnosticquestion_id' => $question->id,
                        ],
                        [
                            'score_total' => $scoreObtenu,
                            'score_max' => $scoreMax,
                            'score_pourcentage' => $pourcentage,
                            'diagnosticblocstatut_id' => $this->determinerStatutBloc($pourcentage),
                        ],
                        ['diagnostic_id', 'diagnosticmodule_id', 'diagnosticquestion_id']
                    );

                    // Chercher les templates correspondants pour cette question
                    $templates = Plantemplate::where('diagnosticquestion_id', $question->id)
                        ->where('niveau', $niveauQuestion)
                        ->actif()
                        ->get();

                    // Si pas de template spécifique à la question, chercher les templates du module
                    if ($templates->isEmpty()) {
                        $templates = Plantemplate::where('diagnosticmodule_id', $module->id)
                            ->whereNull('diagnosticquestion_id')
                            ->where('niveau', $niveauQuestion)
                            ->actif()
                            ->get();
                    }

                    \Log::info('Templates trouvés', [
                        'question_id' => $question->id,
                        'module_id' => $module->id,
                        'niveau_calcule' => $niveauQuestion,
                        'templates_count' => $templates->count()
                    ]);

                    foreach ($templates as $template) {
                        // Vérifier si le plan n'existe pas déjà
                        $planExistant = Plan::where('accompagnement_id', $accompagnement->id)
                            ->where('plantemplate_id', $template->id)
                            ->where('objectif', $template->objectif)
                            ->where('actionprioritaire', $template->actionprioritaire)
                            ->exists();

                        if (!$planExistant) {
                            $plan = Plan::create([
                                'objectif' => $template->objectif,
                                'actionprioritaire' => $template->actionprioritaire,
                                'dateplan' => now()->addDays((int)$template->priorite * 7), // Priorité = délai en semaines
                                'accompagnement_id' => $accompagnement->id,
                                'plantemplate_id' => $template->id,
                                'diagnosticmodule_id' => $template->diagnosticmodule_id, // Récupérer le module depuis le template
                                'diagnosticquestion_id' => $template->diagnosticquestion_id, // Récupérer la question depuis le template
                                'etat' => 1,
                                'spotlight' => 0,
                            ]);
                            

                            \Log::info('Plan créé', [
                                'plan_id' => $plan->id,
                                'template_id' => $template->id,
                                'question_id' => $question->id,
                                'objectif' => $template->objectif
                            ]);
                            

                            $plansCrees++;
                        } else {
                            \Log::info('Plan déjà existant', [
                                'template_id' => $template->id,
                                'question_id' => $question->id,
                                'objectif' => $template->objectif
                            ]);
                        }
                    }
                }
            }
        }

        \Log::info("Génération automatique terminée : {$plansCrees} plans créés pour le diagnostic {$diagnostic->id}");
        
        // Mettre à jour les plans existants qui n'ont pas de module
        $this->mettreAJourPlansExistants($diagnostic);
        
    } catch (\Exception $e) {
        \Log::error("Erreur lors de la génération automatique des plans : " . $e->getMessage(), [
            'diagnostic_id' => $diagnostic->id ?? 'unknown',
            'trace' => $e->getTraceAsString()
        ]);
        // Ne pas bloquer le processus de diagnostic
    }
}

/**
     * Met à jour les plans existants pour leur assigner le bon diagnosticmodule_id et diagnosticquestion_id
     * basé sur leur plantemplate associé
     */
    private function mettreAJourPlansExistants($diagnostic)
    {
        try {
            $plansMaj = 0;
            
            // Récupérer tous les plans de l'accompagnement qui n'ont pas de diagnosticmodule_id
            $plans = Plan::where('accompagnement_id', $diagnostic->accompagnement->id)
                ->whereNotNull('plantemplate_id')
                ->whereNull('diagnosticmodule_id')
                ->with('plantemplate')
                ->get();
                
            foreach ($plans as $plan) {
                if ($plan->plantemplate) {
                    $plan->update([
                        'diagnosticmodule_id' => $plan->plantemplate->diagnosticmodule_id,
                        'diagnosticquestion_id' => $plan->plantemplate->diagnosticquestion_id,
                    ]);
                    $plansMaj++;
                }
            }
            
            \Log::info("Mise à jour des plans existants : {$plansMaj} plans mis à jour pour le diagnostic {$diagnostic->id}");
            
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la mise à jour des plans existants : " . $e->getMessage());
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
