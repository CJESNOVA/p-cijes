<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Diagnostic;
use App\Models\Diagnosticresultat;
use App\Models\Diagnosticquestion;
use App\Models\Diagnosticreponse;
use App\Models\Diagnosticmodule;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiagnosticController extends Controller
{
    /**
     * Récupère tous les diagnostics complets pour un utilisateur via son email
     * 
     * @param string $email
     * @return JsonResponse
     */
    public function getDiagnosticComplet($email): JsonResponse
    {
        try {
            // Récupérer l'utilisateur par son email
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun utilisateur trouvé avec cet email',
                    'data' => null
                ], 404);
            }

            // Récupérer le membre associé à l'utilisateur
            $membre = Membre::where('user_id', $user->id)->first();
            
            if (!$membre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun membre trouvé pour cet utilisateur',
                    'data' => null
                ], 404);
            }

            // Préparer la structure de données
            $data = [
                'user_email' => $email,
                'user_id' => $user->id,
                'membre' => [
                    'id' => $membre->id,
                    'numero_identifiant' => $membre->numero_identifiant,
                    'nom' => $membre->nom,
                    'prenom' => $membre->prenom,
                    'email' => $membre->email,
                    'telephone' => $membre->telephone,
                    'nom_complet' => $membre->nom_complet,
                    'diagnostics' => []
                ]
            ];

            // Ajouter les diagnostics du membre (type 1 - diagnostics personnels)
            $diagnosticsMembre = Diagnostic::where('membre_id', $membre->id)
                ->where('diagnostictype_id', 1)
                ->with(['diagnostictype', 'diagnosticstatut', 'entrepriseprofil'])
                ->get();

            if ($diagnosticsMembre->isNotEmpty()) {
                $data['membre']['diagnostics'] = $this->formatDiagnostics($diagnosticsMembre);
            }

            return response()->json([
                'success' => true,
                'message' => 'Données de diagnostic récupérées avec succès',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Formate les diagnostics avec toutes leurs données associées
     * 
     * @param \Illuminate\Database\Eloquent\Collection $diagnostics
     * @return array
     */
    private function formatDiagnostics($diagnostics): array
    {
        $formattedDiagnostics = [];

        foreach ($diagnostics as $diagnostic) {
            // Récupérer tous les résultats pour ce diagnostic
            $resultats = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->with([
                    'diagnosticquestion' => function($query) {
                        $query->with(['diagnosticmodule.diagnosticmodulecategory', 'diagnosticquestiontype', 'diagnosticquestioncategorie'])
                              ->whereHas('diagnosticmodule', function($moduleQuery) {
                                  $moduleQuery->where('diagnosticmoduletype_id', 1);
                              });
                    },
                    'diagnosticreponse'
                ])
                ->get();

            // Grouper les résultats par module
            $modulesData = [];
            foreach ($resultats as $resultat) {
                // Vérifier que la question et le module existent
                if (!$resultat->diagnosticquestion || !$resultat->diagnosticquestion->diagnosticmodule) {
                    continue;
                }
                
                $module = $resultat->diagnosticquestion->diagnosticmodule;
                $moduleId = $module->id;
                
                // Créer le module s'il n'existe pas
                if (!isset($modulesData[$moduleId])) {
                    $category = $module->diagnosticmodulecategory;
                    $modulesData[$moduleId] = [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'position' => $module->position,
                        'categorie' => [
                            'id' => $category ? $category->id : null,
                            'titre' => $category ? $category->titre : 'Non catégorisé'
                        ],
                        'questions' => []
                    ];
                }

                $questionData = [
                    'id' => $resultat->diagnosticquestion->id,
                    'titre' => $resultat->diagnosticquestion->titre,
                    'position' => $resultat->diagnosticquestion->position,
                    'obligatoire' => $resultat->diagnosticquestion->obligatoire,
                    'type' => $resultat->diagnosticquestion->diagnosticquestiontype->titre ?? null,
                    'categorie' => $resultat->diagnosticquestion->diagnosticquestioncategorie->titre ?? null,
                    'reponse' => [
                        'id' => $resultat->diagnosticreponse->id ?? null,
                        'titre' => $resultat->diagnosticreponse->titre ?? null,
                        'score' => $resultat->diagnosticreponse->score ?? 0,
                        'position' => $resultat->diagnosticreponse->position ?? null
                    ],
                    'reponse_texte' => $resultat->reponsetexte,
                    'reponse_ids' => $resultat->diagnosticreponseids
                ];

                $modulesData[$moduleId]['questions'][] = $questionData;
            }

            // Convertir les modules en tableau indexé
            $modulesData = array_values($modulesData);

            // Calculer les statistiques par module
            foreach ($modulesData as &$module) {
                $scoreTotal = 0;
                $scoreMaximum = 0;
                $questionsRepondues = 0;

                foreach ($module['questions'] as $question) {
                    if ($question['reponse']['score'] !== null) {
                        $scoreTotal += $question['reponse']['score'];
                        $questionsRepondues++;
                    }
                    // Calculer le score maximum pour cette question
                    $maxQuestionScore = Diagnosticreponse::where('diagnosticquestion_id', $question['id'])
                        ->max('score') ?? 0;
                    $scoreMaximum += $maxQuestionScore;
                }

                $module['statistiques'] = [
                    'score_total' => $scoreTotal,
                    'score_maximum' => $scoreMaximum,
                    'pourcentage' => $scoreMaximum > 0 ? round(($scoreTotal / $scoreMaximum) * 100, 2) : 0,
                    'questions_repondues' => $questionsRepondues,
                    'nombre_questions' => count($module['questions'])
                ];
            }
            
            // Récupérer les plans d'accompagnement pour ce diagnostic (une seule fois)
            $plansAccompagnement = [];
            if ($diagnostic->entreprise_id) {
                // Rechercher les accompagnements liés à ce diagnostic
                $accompagnement = \App\Models\Accompagnement::where('diagnostic_id', $diagnostic->id)
                    ->where('etat', 1)
                    ->first();
                
                if ($accompagnement) {
                    $plansAccompagnement = Plan::where('accompagnement_id', $accompagnement->id)
                        ->where('etat', 1) // Uniquement les plans actifs
                        ->orderBy('dateplan', 'asc')
                        ->get()
                        ->map(function($plan) {
                            return [
                                'id' => $plan->id,
                                'objectif' => $plan->objectif,
                                'action_prioritaire' => $plan->actionprioritaire,
                                'date_plan' => $plan->dateplan,
                                'spotlight' => $plan->spotlight,
                                'etat' => $plan->etat
                            ];
                        });
                }
            }

            // Calculer les statistiques globales
            $globalScoreTotal = 0;
            $globalScoreMaximum = 0;
            $totalQuestions = 0;
            $totalQuestionsRepondues = 0;
            $totalModules = 0;

            foreach ($modulesData as $module) {
                if (isset($module['statistiques'])) {
                    $globalScoreTotal += $module['statistiques']['score_total'];
                    $globalScoreMaximum += $module['statistiques']['score_maximum'];
                    $totalQuestions += $module['statistiques']['nombre_questions'];
                    $totalQuestionsRepondues += $module['statistiques']['questions_repondues'];
                    $totalModules++;
                }
            }
            
            $globalPourcentage = $globalScoreMaximum > 0 ? round(($globalScoreTotal * 100) / $globalScoreMaximum, 2) : 0;
            
            $diagnosticData = [
                'id' => $diagnostic->id,
                'score_global' => $diagnostic->scoreglobal,
                'commentaire' => $diagnostic->commentaire,
                'etat' => $diagnostic->etat,
                'spotlight' => $diagnostic->spotlight,
                'type' => $diagnostic->diagnostictype->titre ?? null,
                'statut' => $diagnostic->diagnosticstatut->titre ?? null,
                'profil' => $diagnostic->entrepriseprofil->titre ?? null,
                'modules' => $modulesData,
                'statistiques_globales' => [
                    'score_total' => $globalScoreTotal,
                    'score_maximum' => $globalScoreMaximum,
                    'pourcentage' => $globalPourcentage,
                    'nombre_modules' => $totalModules,
                    'nombre_total_questions' => $totalQuestions,
                    'questions_repondues_total' => $totalQuestionsRepondues
                ],
                'plans_accompagnement' => is_array($plansAccompagnement) ? $plansAccompagnement : $plansAccompagnement->toArray()
            ];

            $formattedDiagnostics[] = $diagnosticData;
        }

        return $formattedDiagnostics;
    }
}
