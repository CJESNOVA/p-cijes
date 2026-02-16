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
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Entrepriseprofil;
use App\Models\Accompagnement;
use App\Models\Diagnosticevolution;

use App\Services\RecompenseService;
use App\Services\DiagnosticStatutService;

class DiagnosticentrepriseController extends Controller
{
    protected $diagnosticStatutService;

    public function __construct(DiagnosticStatutService $diagnosticStatutService)
    {
        $this->diagnosticStatutService = $diagnosticStatutService;
    }

    public function indexForm()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
        }

        $entrepriseMembres = Entreprisemembre::with('entreprise')
            ->where('membre_id', $membre->id)
            ->get();

        $entreprises = $entrepriseMembres->pluck('entreprise');

        return view('diagnosticentreprise.choix_entreprise', compact('entreprises'));
    }
    
    public function showForm($entrepriseId, $moduleId = null)
    {
        // Récupérer l'entreprise AVEC son profil
        $entreprise = Entreprise::with('entrepriseprofil')->findOrFail($entrepriseId);
        
        // Récupération de TOUS les modules type 2 (diagnostic entreprise), filtrés par profil
        $allDiagnosticmodules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
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
        
        // 🔧 Correction du calcul de l'index pour éviter false
        $currentModuleIndex = null;
        foreach ($allDiagnosticmodules as $index => $module) {
            if ($module->id == $moduleId) {
                $currentModuleIndex = $index;
                break;
            }
        }
        
        $nextModule = $currentModuleIndex !== null ? $allDiagnosticmodules->get($currentModuleIndex + 1) : null;
        $previousModule = $currentModuleIndex > 0 ? $allDiagnosticmodules->get($currentModuleIndex - 1) : null;
        $isLastModule = ($currentModuleIndex + 1) >= $allDiagnosticmodules->count();

        // 🔧 Ajouter la session showFinalization quand on est au dernier module
        if ($isLastModule) {
            session(['showFinalization' => true]);
        }

        // Diagnostic existant pour cette entreprise (non terminé)
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
            ->where('diagnosticstatut_id', 1)
            ->where('diagnostictype_id', 2) 
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

        return view('diagnosticentreprise.form', compact(
            'modules',
            'currentModule',
            'nextModule',
            'previousModule',
            'isLastModule',
            'existing',
            'diagnostic',
            'entrepriseId',
            'entreprise'
        ));
    }

    public function saveModule(Request $request, $entrepriseId, $moduleId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
        }

        // Récupérer l'entreprise avec son profil pour le filtrage
        $entreprise = Entreprise::with('entrepriseprofil')->findOrFail($entrepriseId);

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

        // Récupérer le dernier diagnostic en cours pour cette entreprise
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id)
            ->where('diagnosticstatut_id', 1) // Non terminé
            ->where('diagnostictype_id', 2)
            ->orderBy('created_at', 'desc')
            ->first();

        // Si aucun diagnostic en cours, en créer un nouveau
        if (!$diagnostic) {
            $diagnostic = Diagnostic::create([
                'entreprise_id' => $entrepriseId,
                'membre_id' => $membre->id,
                'diagnosticstatut_id' => 1,
                'diagnostictype_id' => 2,
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
            $allModules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
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
        $allModules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
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
            return redirect()->route('diagnosticentreprise.showModule', [$entrepriseId, $nextModule->id])
                ->with('success', "✅ Module {$moduleActuel}/{$totalModules} enregistré avec succès ! Continuez sur le module suivant.");
        } else {
            return redirect()->back()
                ->with('success', '✅ Dernier module enregistré ! Vous pouvez maintenant finaliser le diagnostic.')
                ->with('showFinalization', true);
        }
    }

    public function store(Request $request, RecompenseService $recompenseService, $entrepriseId = null, $moduleId = null)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
    }

    // Récupérer l'entreprise avec son profil pour le filtrage
    $entreprise = Entreprise::with('entrepriseprofil')->findOrFail($entrepriseId);

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

    // Récupérer le dernier diagnostic en cours pour cette entreprise
    $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
        ->where('membre_id', $membre->id)
        ->where('diagnosticstatut_id', 1) // Non terminé
        ->where('diagnostictype_id', 2)
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
    $allModules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
        ->where('etat', 1)
        ->orderByRaw('CAST(position AS UNSIGNED)') // Cast en nombre pour tri numérique
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
                $modulesAvecQuestionsManquantes[] = ($index + 1);
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
    \DB::transaction(function () use ($diagnostic, $entrepriseId, $membre, $totalScore) {
        $diagnostic->update([
            'scoreglobal' => $totalScore,
            'diagnosticstatut_id' => 2,
            'entrepriseprofil_id' => $diagnostic->entreprise->entrepriseprofil_id,
        ]);

        // 🏁 Création systématique d'un nouvel accompagnement pour chaque diagnostic
        $accompagnement = Accompagnement::create([
            'entreprise_id' => $entrepriseId,
            'membre_id' => $membre->id,
            'accompagnementniveau_id' => 1,
            'dateaccompagnement' => now(),
            'accompagnementstatut_id' => 1,
        ]);

        $diagnostic->update([
            'accompagnement_id' => $accompagnement->id,
        ]);

        // 🎯 GÉNÉRATION AUTOMATIQUE DES PLANS D'ACCOMPAGNEMENT
        $this->genererPlansAutomatiques($diagnostic);
    });

    // 💯 Diagnostic terminé - Évaluer le profil APRÈS la mise à jour
    \Log::info('Début évaluation profil entreprise', [
        'entreprise_id' => $entrepriseId,
        'diagnostic_id' => $diagnostic->id,
        'diagnostic_statut' => $diagnostic->diagnosticstatut_id,
        'score_global' => $totalScore
    ]);
    
    $resultatProfil = $this->diagnosticStatutService->evaluerProfilEntreprise($entrepriseId, false, $diagnostic->id);
    
    \Log::info('Résultat évaluation profil', [
        'resultat' => $resultatProfil,
        'changement_effectue' => $resultatProfil['changement_effectue'] ?? false,
        'message' => $resultatProfil['message'] ?? 'Pas de message'
    ]);
    
    $messageSucces = "✅ Diagnostic terminé. Score : {$totalScore}";
    if ($resultatProfil['changement_effectue']) {
        $messageSucces .= " | " . $resultatProfil['message'];
    }

    // 🏆 Vérifie si c'est le premier diagnostic PME du membre
    $nbDiagnostics = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', $entrepriseId)
        ->where('diagnosticstatut_id', 2)
        ->count();

    if ($nbDiagnostics === 1) {
        // 🪙 Déclenche la récompense "DIAG_ENTREPRISE_COMPLET"
        $recompense = $recompenseService->attribuerRecompense('DIAG_ENTREPRISE_COMPLET', $membre, $entreprise ?? null, $diagnostic->id);
    }

    return redirect("/diagnostics/diagnosticentreprise/success/{$diagnostic->id}")
        ->with('success', $messageSucces);
}

    /**
     * Évaluer manuellement le profil d'une entreprise
     */
    public function evaluerProfil($entrepriseId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 403);
        }

        // Vérifier que le membre a accès à cette entreprise
        $entrepriseMembre = Entreprisemembre::where('membre_id', $membre->id)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        if (!$entrepriseMembre && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à cette entreprise'
            ], 403);
        }

        try {
            $resultat = $this->diagnosticStatutService->evaluerProfilEntreprise($entrepriseId);
            
            return response()->json([
                'success' => true,
                'data' => $resultat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'évaluation : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir l'historique des profils d'une entreprise
     */
    public function getHistoriqueProfils($entrepriseId, $limit = 10)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 403);
        }

        // Vérifier que le membre a accès à cette entreprise
        $entrepriseMembre = Entreprisemembre::where('membre_id', $membre->id)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        if (!$entrepriseMembre && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à cette entreprise'
            ], 403);
        }

        try {
            $evolutions = $this->diagnosticStatutService->getEvolutions($entrepriseId, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $evolutions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des évolutions : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche la page de succès avec les détails du diagnostic
     */
    public function success($diagnosticId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        // Récupérer le diagnostic avec toutes ses relations
        $diagnostic = Diagnostic::where('id', $diagnosticId)
            ->where('diagnostictype_id', 2) // diagnostic entreprise
            ->with([
                'entreprise',
                'accompagnement',
                'diagnosticresultats.diagnosticquestion.diagnosticmodule',
                'diagnosticresultats.diagnosticreponse',
                'diagnosticmodulescores.diagnosticmodule'
            ])
            ->firstOrFail();

        // Vérifier que le diagnostic appartient au membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)->pluck('entreprise_id');
        if ($diagnostic->membre_id != $membre->id && !in_array($diagnostic->entreprise_id, $entrepriseIds->toArray())) {
            return redirect()->route('diagnosticentreprise.indexForm')
                ->with('error', 'Accès non autorisé à ce diagnostic.');
        }

        // Récupérer tous les modules pour l'affichage (filtrés par profil d'entreprise)
        $modules = $this->getModulesForProfil($diagnostic->entreprise->entrepriseprofil_id, 2)
            ->with(['diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                  ->orderByRaw('CAST(position AS UNSIGNED)')
                  ->with(['diagnosticreponses' => fn($query) => $query->where('etat', 1)]);
            }])
            ->get();

        return view('diagnosticentreprise.success', compact('diagnostic', 'modules'));
    }

    /**
     * Affiche la liste des plans d'accompagnement pour un diagnostic entreprise
     */
    public function listePlans($diagnosticId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Récupérer les entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id')
            ->toArray();

        // Vérifier que le diagnostic appartient au membre ou à ses entreprises
        $diagnostic = Diagnostic::where('id', $diagnosticId)
            ->where(function ($query) use ($membre, $entrepriseIds) {
                $query->where('membre_id', $membre->id)
                      ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->with(['accompagnement.plans', 'diagnosticmodulescores.diagnosticmodule'])
            ->firstOrFail();

        return view('diagnosticentreprise.plans', compact('diagnostic'));
    }

    /**
     * Convertit un score de réponse (1-4) en niveau (A-D)
     * Note: D est la valeur maximale dans notre système
     */
    private function convertirScoreEnNiveau($score)
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
     * Calcule le score cumulé pour un module basé sur toutes les réponses
     */
    private function calculerScoreCumuleModule($diagnosticId, $moduleId)
    {
        // Récupérer toutes les réponses pour ce module
        $reponses = Diagnosticresultat::where('diagnostic_id', $diagnosticId)
            ->whereHas('diagnosticquestion', function($q) use ($moduleId) {
                $q->where('diagnosticmodule_id', $moduleId);
            })
            ->with('diagnosticreponse')
            ->get();

        // Débogage détaillé
        \Log::info('Réponses pour module ' . $moduleId, [
            'module_id' => $moduleId,
            'nombre_reponses' => $reponses->count(),
            'reponses' => $reponses->map(function($reponse) {
                return [
                    'question_id' => $reponse->diagnosticquestion_id,
                    'reponse_score' => $reponse->diagnosticreponse->score ?? 0,
                    'reponse_texte' => $reponse->diagnosticreponse->texte ?? 'N/A'
                ];
            })->toArray()
        ]);

        if ($reponses->isEmpty()) {
            return 0;
        }

        // Calculer le score cumulé (somme des scores de chaque réponse)
        $scoreTotal = $reponses->sum(function($reponse) {
            return $reponse->diagnosticreponse->score ?? 0;
        });
        
        \Log::info('Score cumulé calculé pour module ' . $moduleId, [
            'module_id' => $moduleId,
            'score_total' => $scoreTotal,
            'nombre_reponses' => $reponses->count()
        ]);
        
        return $scoreTotal;
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
            // Récupérer l'accompagnement
            $accompagnement = Accompagnement::find($diagnostic->accompagnement_id);
            
            if (!$accompagnement) {
                \Log::warning('Aucun accompagnement trouvé pour le diagnostic ' . $diagnostic->id);
                return;
            }

            // Récupérer l'entreprise avec son profil
            $entreprise = Entreprise::with('entrepriseprofil')->find($diagnostic->entreprise_id);

            // Récupérer tous les modules du diagnostic (type 2 pour entreprise, filtrés par profil)
            $modules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
                ->whereHas('diagnosticquestions', function($q) use ($diagnostic) {
                    $q->whereHas('diagnosticresultats', function($subQ) use ($diagnostic) {
                        $subQ->where('diagnostic_id', $diagnostic->id);
                    });
                })
                ->get();

            $plansCrees = 0;
            
            foreach ($modules as $module) {
                // Calculer le vrai score cumulé pour ce module
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

                \Log::info('Score module créé/mis à jour (entreprise)', [
                    'diagnostic_id' => $diagnostic->id,
                    'module_id' => $module->id,
                    'score_total' => $scoreCalcule['score_total'],
                    'score_max' => $scoreCalcule['score_max'],
                    'score_pourcentage' => $scoreCalcule['score_pourcentage'],
                    'module_score_id' => $moduleScore->id,
                    'was_created' => $moduleScore->wasRecentlyCreated
                ]);

                // Chercher les templates correspondants
                $niveauCalcule = $this->convertirScoreEnNiveau($scoreCalcule['score_total']);
                $templates = Plantemplate::where('diagnosticmodule_id', $module->id)
                    ->where('niveau', $niveauCalcule)
                    ->actif()
                    ->get();

                \Log::info('Templates trouvés (entreprise)', [
                    'module_id' => $module->id,
                    'niveau' => $niveauCalcule,
                    'templates_count' => $templates->count()
                ]);

                foreach ($templates as $template) {
                    \Log::info('Traitement template (entreprise)', [
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
                        Plan::create([
                            'objectif' => $template->objectif,
                            'actionprioritaire' => $template->actionprioritaire,
                            'dateplan' => now()->addDays((int)$template->priorite * 7), // Priorité = délai en semaines
                            'accompagnement_id' => $accompagnement->id,
                            'etat' => 1,
                            'spotlight' => 0,
                        ]);
                        $plansCrees++;
                    }
                }
            }

            \Log::info("Génération automatique entreprise : {$plansCrees} plans créés pour le diagnostic {$diagnostic->id}");
            
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la génération automatique des plans entreprise : " . $e->getMessage());
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
    private function convertirNiveauEnScore($niveau)
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
     * Récupère les modules de diagnostic selon le profil d'entreprise
     * @param int|null $profilId ID du profil d'entreprise
     * @param int $typeId Type de diagnostic (1: PME, 2: Entreprise, 3: Qualification)
     * @return Builder
     */
    private function getModulesForProfil($profilId, $typeId)
    {
        return Diagnosticmodule::where('diagnosticmoduletype_id', $typeId)
            ->where('etat', 1)
            ->when($profilId, function($query) use ($profilId) {
                // Modules spécifiques à ce profil d'entreprise
                // OU modules généraux (tous profils) du même type
                return $query->where(function($subQuery) use ($profilId) {
                    $subQuery->where('entrepriseprofil_id', $profilId)
                             ->orWhereNull('entrepriseprofil_id');
                });
            })
            ->orderBy('position');
    }


}
