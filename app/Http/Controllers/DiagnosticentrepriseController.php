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
    protected $recompenseService;

    public function __construct(RecompenseService $recompenseService)
    {
        $this->recompenseService = $recompenseService;
        
        // Créer le DiagnosticStatutService avec le RecompenseService injecté
        $this->diagnosticStatutService = new DiagnosticStatutService($recompenseService);
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

        // 🔍 Vérifier si le membre a au moins une entreprise avec un test de classification validé
        $entreprisesAvecQualification = Diagnostic::where('membre_id', $membre->id)
            ->where('diagnostictype_id', 3) // Test de classification
            ->where('diagnosticstatut_id', 2) // Terminé/Validé
            ->pluck('entreprise_id')
            ->toArray();

        // Filtrer les entreprises pour n'afficher que celles avec classification valide
        $entreprises = $entreprises->filter(function($entreprise) use ($entreprisesAvecQualification) {
            return in_array($entreprise->id, $entreprisesAvecQualification);
        });

        if ($entreprises->isEmpty()) {
            return redirect()->route('diagnosticentreprisequalification.indexForm')
                ->with('error', '⚠️ Vous devez d\'abord compléter et valider le test de classification pour au moins une entreprise avant de pouvoir accéder au diagnostic d\'entreprise.');
        }

        return view('diagnosticentreprise.choix_entreprise', compact('entreprises'));
    }
    
    public function showForm($entrepriseId, $moduleId = null)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
        }

        // Récupérer l'entreprise AVEC son profil
        $entreprise = Entreprise::with('entrepriseprofil')->find($entrepriseId);
        
        if (!$entreprise) {
            \Log::warning('Entreprise non trouvée dans showForm, entrepriseId: ' . $entrepriseId);
            return redirect()->route('diagnosticentreprise.indexForm')
                ->with('error', '⚠️ Entreprise non trouvée. Veuillez sélectionner une entreprise valide.');
        }

        // 🔍 Vérifier si le membre a un test de classification validé pour cette entreprise
        $qualificationValidee = Diagnostic::where('membre_id', $membre->id)
            ->where('entreprise_id', $entrepriseId)
            ->where('diagnostictype_id', 3) // Test de classification
            ->where('diagnosticstatut_id', 2) // Terminé/Validé
            ->exists();

        if (!$qualificationValidee) {
            return redirect()->route('diagnosticentreprisequalification.indexForm')
                ->with('error', '⚠️ Vous devez d\'abord compléter et valider le test de classification pour cette entreprise avant de pouvoir accéder au diagnostic d\'entreprise.');
        }
        
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
        $entreprise = Entreprise::with('entrepriseprofil')->find($entrepriseId);
        
        if (!$entreprise) {
            \Log::warning('Entreprise non trouvée dans saveModule, entrepriseId: ' . $entrepriseId);
            return redirect()->back()
                ->with('error', '⚠️ Entreprise non trouvée. Veuillez vérifier votre sélection.')
                ->withInput();
        }

        // 🔍 Vérifier si le membre a un test de classification validé pour cette entreprise
        $qualificationValidee = Diagnostic::where('membre_id', $membre->id)
            ->where('entreprise_id', $entrepriseId)
            ->where('diagnostictype_id', 3) // Test de classification
            ->where('diagnosticstatut_id', 2) // Terminé/Validé
            ->exists();

        if (!$qualificationValidee) {
            return redirect()->route('diagnosticentreprisequalification.indexForm')
                ->with('error', '⚠️ Vous devez d\'abord compléter et valider le test de classification pour cette entreprise avant de pouvoir accéder au diagnostic d\'entreprise.');
        }

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
        $diagnostic = Diagnostic::with('accompagnement')->where('entreprise_id', $entrepriseId)
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
                'entrepriseprofil_id' => $membre->entrepriseprofil_id,
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

        // Calculer et enregistrer les scores par question pour ce module
        $this->creerScoresParQuestionFromModule($diagnostic->id, $moduleId);

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
    try {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
        }

        // Récupérer le module_id depuis la requête POST si non fourni en paramètre d'URL
        $moduleId = $moduleId ?: $request->input('module_id');

        // Récupérer l'entreprise avec son profil pour le filtrage
        $entreprise = Entreprise::with('entrepriseprofil')->find($entrepriseId);
        
        if (!$entreprise) {
            \Log::warning('Entreprise non trouvée dans store, entrepriseId: ' . $entrepriseId);
            return redirect()->back()
                ->with('error', '⚠️ Entreprise non trouvée. Veuillez vérifier votre sélection.')
                ->withInput();
        }

        // 🔍 Vérifier si le membre a un test de classification validé pour cette entreprise
        $qualificationValidee = Diagnostic::where('membre_id', $membre->id)
            ->where('entreprise_id', $entrepriseId)
            ->where('diagnostictype_id', 3) // Test de classification
            ->where('diagnosticstatut_id', 2) // Terminé/Validé
            ->exists();

        if (!$qualificationValidee) {
            return redirect()->route('diagnosticentreprisequalification.indexForm')
                ->with('error', '⚠️ Vous devez d\'abord compléter et valider le test de classification pour cette entreprise avant de pouvoir accéder au diagnostic d\'entreprise.');
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

    // Récupérer le dernier diagnostic en cours pour cette entreprise
    $diagnostic = Diagnostic::with('accompagnement')->where('entreprise_id', $entrepriseId)
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
            'diagnostic_id' => $diagnostic->id,
            'accompagnementniveau_id' => 1,
            'dateaccompagnement' => now(),
            'accompagnementstatut_id' => 1,
        ]);

        // 🎯 GÉNÉRATION AUTOMATIQUE DES PLANS D'ACCOMPAGNEMENT
        $this->genererPlansAutomatiques($diagnostic, $accompagnement);
        
        // 📈 CRÉATION DE L'ÉVOLUTION DIAGNOSTIC
        $derniereEvolution = Diagnosticevolution::where('entreprise_id', $entrepriseId)
            ->orderBy('created_at', 'desc')
            ->first();
            
        Diagnosticevolution::creerEvolution(
            $entrepriseId,
            $diagnostic->id,
            $derniereEvolution ? $derniereEvolution->diagnostic_id : null,
            'Finalisation du diagnostic entreprise'
        );
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
        // 🪙 Déclenche la récompense "DIAG_ENTREPRISE"
        // 💡 Utiliser le score global du diagnostic comme base pour le calcul en pourcentage
        $recompense = $recompenseService->attribuerRecompense('DIAG_ENTREPRISE', $membre, $entreprise ?? null, $diagnostic->id, null);
        
        // 🏆 Ajout du paiement PREMIER_DIAG_ENTREPRISE
        $moduleController = new \App\Http\Controllers\ModuleRessourceController();
        $resultatModule = $moduleController->attribuerModuleViaAction(
            'diagnostics',
            $diagnostic->id,
            'PREMIER_DIAG_ENTREPRISE',
            $membre,
            [
                'entreprise' => $entreprise,
                'description' => 'Premier diagnostic entreprise terminé',
                'reference' => 'PREMIER-ENT-' . $diagnostic->id . '-' . date('YmdHis')
            ]
        );
        
        \Log::info('Paiement PREMIER_DIAG_ENTREPRISE effectué', [
            'success' => $resultatModule['success'],
            'message' => $resultatModule['message'] ?? 'OK'
        ]);
    } else {
        // 🏁 Déclenche le paiement AUTRE_DIAG_ENTREPRISE pour les diagnostics suivants
        \Log::info('Diagnostic entreprise suivant détecté - Attribution AUTRE_DIAG_ENTREPRISE');
        
        $moduleController = new \App\Http\Controllers\ModuleRessourceController();
        $resultatModule = $moduleController->attribuerModuleViaAction(
            'diagnostics',
            $diagnostic->id,
            'AUTRE_DIAG_ENTREPRISE',
            $membre,
            [
                'entreprise' => $entreprise,
                'description' => 'Diagnostic entreprise terminé (suivant)',
                'reference' => 'AUTRE-ENT-' . $diagnostic->id . '-' . date('YmdHis')
            ]
        );
        
        \Log::info('Paiement AUTRE_DIAG_ENTREPRISE effectué', [
            'success' => $resultatModule['success'],
            'message' => $resultatModule['message'] ?? 'OK'
        ]);
    }

    return redirect("/diagnostics/diagnosticentreprise/success/{$diagnostic->id}")
        ->with('success', $messageSucces);

    } catch (\Exception $e) {
        \Log::error('Erreur dans la méthode store', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', '⚠️ Une erreur est survenue lors de la finalisation du diagnostic : ' . $e->getMessage())
            ->withInput();
    }
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

        // Calculer les niveaux par module
        $niveauxModules = [];
        foreach ($modules as $module) {
            $niveauxModules[$module->id] = $this->calculerNiveauMoyenModule($diagnostic->id, $module->id);
        }

        return view('diagnosticentreprise.success', compact('diagnostic', 'modules', 'niveauxModules'));
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
            ->with(['accompagnement.plans.plantemplate.diagnosticmodule', 'accompagnement.plans.plantemplate.diagnosticquestion', 'diagnosticmodulescores.diagnosticmodule'])
            ->firstOrFail();

        // Calculer les niveaux par module pour l'affichage
        $niveauxModules = [];
        if ($diagnostic->diagnosticmodulescores->isNotEmpty()) {
            $modulesIds = $diagnostic->diagnosticmodulescores->pluck('diagnosticmodule_id')->unique();
            foreach ($modulesIds as $moduleId) {
                $niveauxModules[$moduleId] = $this->calculerNiveauMoyenModule($diagnostic->id, $moduleId);
            }
        }

        return view('diagnosticentreprise.plans', compact('diagnostic', 'niveauxModules'));
    }

    /**
     * Convertit un score de réponse (1-4) en niveau numérique (1-4)
     * Note: 4 est la valeur maximale dans notre système
     */
    private function convertirScoreEnNiveau($score)
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

        \Log::info('Scores par question créés pour module (entreprise)', [
            'diagnostic_id' => $diagnosticId,
            'module_id' => $moduleId,
            'reponses_count' => $reponses->count()
        ]);
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
    private function genererPlansAutomatiques($diagnostic, $accompagnement = null)
    {
        try {
            \Log::info('Début génération automatique des plans entreprise', [
                'diagnostic_id' => $diagnostic->id,
                'accompagnement_id' => $accompagnement ? $accompagnement->id : null
            ]);
            
            if (!$accompagnement) {
                \Log::warning('Aucun accompagnement trouvé pour le diagnostic ' . $diagnostic->id);
                return;
            }

            // Récupérer l'entreprise avec son profil
            $entreprise = Entreprise::with('entrepriseprofil')->find($diagnostic->entreprise_id);
            
            if (!$entreprise) {
                \Log::warning('Entreprise non trouvée pour le diagnostic ' . $diagnostic->id . ', entreprise_id: ' . $diagnostic->entreprise_id);
                return;
            }

            // Récupérer tous les modules du diagnostic (type 2 pour entreprise, filtrés par profil)
            $modules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
                ->whereHas('diagnosticquestions', function($q) use ($diagnostic) {
                    $q->whereHas('diagnosticresultats', function($subQ) use ($diagnostic) {
                        $subQ->where('diagnostic_id', $diagnostic->id);
                    });
                })
                ->get();

            \Log::info('Modules trouvés (entreprise)', [
                'count' => $modules->count(),
                'modules' => $modules->pluck('id')->toArray(),
                'modules_with_titres' => $modules->pluck('titre', 'id')->toArray()
            ]);

            // Vérifier tous les templates disponibles pour débogage
            $allTemplates = Plantemplate::actif()->get();
            \Log::info('Tous les templates disponibles dans la base', [
                'total_templates' => $allTemplates->count(),
                'templates_par_niveau' => $allTemplates->groupBy('niveau')->map->count(),
                'templates_par_question' => $allTemplates->whereNotNull('diagnosticquestion_id')->count(),
                'templates_par_module' => $allTemplates->whereNotNull('diagnosticmodule_id')->whereNull('diagnosticquestion_id')->count(),
                'exemples' => $allTemplates->take(5)->map(function($t) {
                    return [
                        'id' => $t->id,
                        'niveau' => $t->niveau,
                        'question_id' => $t->diagnosticquestion_id,
                        'module_id' => $t->diagnosticmodule_id,
                        'objectif' => substr($t->objectif, 0, 50) . '...'
                    ];
                })
            ]);

            $plansCrees = 0;
            
            foreach ($modules as $module) {
                // Récupérer les questions du module avec leurs réponses
                $questionsDuModule = Diagnosticquestion::where('diagnosticmodule_id', $module->id)
                    ->where('etat', 1)
                    ->whereHas('diagnosticresultats', function($q) use ($diagnostic) {
                        $q->where('diagnostic_id', $diagnostic->id);
                    })
                    ->with(['diagnosticresultats' => function($q) use ($diagnostic) {
                        $q->where('diagnostic_id', $diagnostic->id)
                          ->with('diagnosticreponse');
                    }])
                    ->get();

                \Log::info('Questions trouvées pour module (entreprise)', [
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
                        
                        \Log::info('Score calculé pour question (entreprise)', [
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
                        \Log::info('Recherche templates pour question', [
                            'question_id' => $question->id,
                            'niveau_recherche' => $niveauQuestion,
                            'question_titre' => $question->titre
                        ]);
                        
                        $templates = Plantemplate::where('diagnosticquestion_id', $question->id)
                            ->where('niveau', $niveauQuestion)
                            ->actif()
                            ->get();

                        \Log::info('Recherche templates par question - Résultat', [
                            'question_id' => $question->id,
                            'niveau' => $niveauQuestion,
                            'templates_trouves' => $templates->count()
                        ]);

                        // Si pas de template spécifique à la question, chercher les templates du module
                        if ($templates->isEmpty()) {
                            \Log::info('Recherche templates pour module', [
                                'module_id' => $module->id,
                                'module_titre' => $module->titre,
                                'niveau_recherche' => $niveauQuestion
                            ]);
                            
                            $templates = Plantemplate::where('diagnosticmodule_id', $module->id)
                                ->whereNull('diagnosticquestion_id')
                                ->where('niveau', $niveauQuestion)
                                ->actif()
                                ->get();
                                
                            \Log::info('Recherche templates par module - Résultat', [
                                'module_id' => $module->id,
                                'niveau' => $niveauQuestion,
                                'templates_trouves' => $templates->count()
                            ]);
                        }

                        foreach ($templates as $template) {
                            // Vérifier si le plan n'existe pas déjà
                            try {
                                \Log::info('Vérification existence plan', [
                                    'accompagnement_id' => $accompagnement->id,
                                    'plantemplate_id' => $template->id,
                                    'objectif' => substr($template->objectif, 0, 50)
                                ]);
                                
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
                                        'etat' => 1,
                                        'spotlight' => 0,
                                    ]);
                                    
                                    \Log::info('Plan créé (entreprise)', [
                                        'plan_id' => $plan->id,
                                        'template_id' => $template->id,
                                        'question_id' => $question->id,
                                        'objectif' => $template->objectif
                                    ]);
                                    
                                    $plansCrees++;
                                } else {
                                    \Log::info('Plan déjà existant (entreprise)', [
                                        'template_id' => $template->id,
                                        'question_id' => $question->id,
                                        'objectif' => $template->objectif
                                    ]);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Erreur lors de la gestion du plan', [
                                    'error' => $e->getMessage(),
                                    'template_id' => $template->id,
                                    'question_id' => $question->id,
                                    'accompagnement_id' => $accompagnement->id
                                ]);
                                // Continuer avec le template suivant
                            }
                        }
                }
            }

            \Log::info("Génération automatique entreprise terminée : {$plansCrees} plans créés pour le diagnostic {$diagnostic->id}");
        }
            
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la génération automatique des plans entreprise : " . $e->getMessage(), [
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
     * @param int $typeId Type de diagnostic (1: PME, 2: Entreprise, 3: Classification)
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
