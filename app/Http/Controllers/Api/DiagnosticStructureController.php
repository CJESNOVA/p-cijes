<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosticmodule;
use App\Models\Diagnosticquestion;
use App\Models\Diagnosticreponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiagnosticStructureController extends Controller
{
    /**
     * Récupère la structure complète des diagnostics
     * Modules -> Questions -> Réponses possibles
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getStructureComplete(Request $request): JsonResponse
    {
        try {
            // Récupérer tous les modules avec leurs questions, réponses et catégories
            $modules = Diagnosticmodule::with(['questions.reponses', 'diagnosticmodulecategory'])
                ->where('etat', 1)
                ->where('diagnosticmoduletype_id', 1)
                ->orderBy('position', 'asc')
                ->get();

            $structure = [];

            // Grouper les modules par catégorie
            $modulesByCategory = $modules->groupBy('diagnosticmodulecategory_id');
            
            foreach ($modulesByCategory as $categoryId => $categoryModules) {
                $category = $categoryModules->first()->diagnosticmodulecategory;
                
                $categoryData = [
                    'id' => $categoryId,
                    'titre' => $category ? $category->titre : 'Non catégorisé',
                    'modules' => []
                ];

                foreach ($categoryModules as $module) {
                    $moduleData = [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description ?? null,
                        'ordre' => $module->position,
                        'diagnosticmoduletype_id' => $module->diagnosticmoduletype_id,
                        'diagnosticmodulecategory_id' => $module->diagnosticmodulecategory_id,
                        'entrepriseprofil_id' => $module->entrepriseprofil_id,
                        'questions' => []
                    ];

                    foreach ($module->questions as $question) {
                        $questionData = [
                            'id' => $question->id,
                            'titre' => $question->titre,
                            'description' => $question->description ?? null,
                            'ordre' => $question->position,
                            'diagnosticmodule_id' => $question->diagnosticmodule_id,
                            'diagnosticquestiontype_id' => $question->diagnosticquestiontype_id,
                            'score_max' => $question->score_max ?? null,
                            'obligatoire' => $question->obligatoire ?? false,
                            'reponses_possibles' => []
                        ];

                        foreach ($question->reponses as $reponse) {
                            $reponseData = [
                                'id' => $reponse->id,
                                'titre' => $reponse->titre,
                                'explication' => $reponse->explication,
                                'description' => $reponse->description ?? null,
                                'score' => $reponse->score,
                                'ordre' => $reponse->position,
                                'diagnosticquestion_id' => $reponse->diagnosticquestion_id
                            ];

                            $questionData['reponses_possibles'][] = $reponseData;
                        }

                        $moduleData['questions'][] = $questionData;
                    }

                    $categoryData['modules'][] = $moduleData;
                }

                $structure[] = $categoryData;
            }

            return response()->json([
                'success' => true,
                'message' => 'Structure complète des diagnostics récupérée avec succès',
                'data' => [
                    'categories' => $structure,
                    'total_categories' => count($structure),
                    'total_modules' => $modules->count(),
                    'total_questions' => $modules->sum(function($module) {
                        return $module->questions->count();
                    }),
                    'total_reponses' => $modules->sum(function($module) {
                        return $module->questions->sum(function($question) {
                            return $question->reponses->count();
                        });
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la structure: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Récupère la structure par module spécifique
     * 
     * @param int $moduleId
     * @return JsonResponse
     */
    public function getStructureByModule($moduleId): JsonResponse
    {
        try {
            $module = Diagnosticmodule::with(['questions.reponses'])
                ->where('id', $moduleId)
                ->where('etat', 1)
                ->where('diagnosticmoduletype_id', 1)
                ->first();

            if (!$module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module non trouvé',
                    'data' => null
                ], 404);
            }

            $moduleData = [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description ?? null,
                'ordre' => $module->position,
                'diagnosticmoduletype_id' => $module->diagnosticmoduletype_id,
                'entrepriseprofil_id' => $module->entrepriseprofil_id,
                'questions' => []
            ];

            foreach ($module->questions as $question) {
                $questionData = [
                    'id' => $question->id,
                    'titre' => $question->titre,
                    'description' => $question->description ?? null,
                    'ordre' => $question->position,
                    'diagnosticmodule_id' => $question->diagnosticmodule_id,
                    'diagnosticquestiontype_id' => $question->diagnosticquestiontype_id,
                    'score_max' => $question->score_max ?? null,
                    'obligatoire' => $question->obligatoire ?? false,
                    'reponses_possibles' => []
                ];

                foreach ($question->reponses as $reponse) {
                    $reponseData = [
                        'id' => $reponse->id,
                        'titre' => $reponse->titre,
                        'explication' => $reponse->explication,
                        'description' => $reponse->description ?? null,
                        'score' => $reponse->score,
                        'ordre' => $reponse->position,
                        'diagnosticquestion_id' => $reponse->diagnosticquestion_id
                    ];

                    $questionData['reponses_possibles'][] = $reponseData;
                }

                $moduleData['questions'][] = $questionData;
            }

            return response()->json([
                'success' => true,
                'message' => 'Structure du module récupérée avec succès',
                'data' => [
                    'module' => $moduleData,
                    'total_questions' => count($moduleData['questions']),
                    'total_reponses' => array_sum(array_map(function($q) {
                        return count($q['reponses_possibles']);
                    }, $moduleData['questions']))
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du module: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Récupère uniquement la liste des modules
     * 
     * @return JsonResponse
     */
    public function getModulesList(): JsonResponse
    {
        try {
            $modules = Diagnosticmodule::where('etat', 1)
                ->where('diagnosticmoduletype_id', 1)
                ->orderBy('position', 'asc')
                ->get(['id', 'titre', 'description', 'position', 'diagnosticmoduletype_id', 'entrepriseprofil_id']);

            return response()->json([
                'success' => true,
                'message' => 'Liste des modules récupérée avec succès',
                'data' => [
                    'modules' => $modules,
                    'total' => $modules->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des modules: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Récupère les questions d'un module spécifique
     * 
     * @param int $moduleId
     * @return JsonResponse
     */
    public function getQuestionsByModule($moduleId): JsonResponse
    {
        try {
            $questions = Diagnosticquestion::with(['reponses'])
                ->where('diagnosticmodule_id', $moduleId)
                ->where('etat', 1)
                ->whereHas('diagnosticmodule', function($query) {
                    $query->where('diagnosticmoduletype_id', 1);
                })
                ->orderBy('position', 'asc')
                ->get();

            $questionsData = [];

            foreach ($questions as $question) {
                $questionData = [
                    'id' => $question->id,
                    'titre' => $question->titre,
                    'description' => $question->description ?? null,
                    'ordre' => $question->position,
                ];

                $questionsData[] = $questionData;
            }

            return response()->json([
                'success' => true,
                'message' => 'Questions du module récupérées avec succès',
                'data' => [
                    'questions' => $questionsData,
                    'total' => count($questionsData)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des questions: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Récupère la structure des diagnostics par profil d'entreprise
     * 
     * @param int $profilId
     * @return JsonResponse
     */
    public function getStructureByProfil($profilId): JsonResponse
    {
        try {
            // Récupérer les modules spécifiques à ce profil avec leurs catégories
            $modules = Diagnosticmodule::with(['questions.reponses', 'diagnosticmodulecategory'])
                ->where('etat', 1)
                ->where('diagnosticmoduletype_id', 1)
                ->where(function($query) use ($profilId) {
                    $query->whereNull('entrepriseprofil_id')
                          ->orWhere('entrepriseprofil_id', $profilId);
                })
                ->orderBy('position', 'asc')
                ->get();

            $structure = [];
            
            // Grouper les modules par catégorie
            $modulesByCategory = $modules->groupBy('diagnosticmodulecategory_id');
            
            foreach ($modulesByCategory as $categoryId => $categoryModules) {
                $category = $categoryModules->first()->diagnosticmodulecategory;
                
                $categoryData = [
                    'id' => $categoryId,
                    'titre' => $category ? $category->titre : 'Non catégorisé',
                    'modules' => []
                ];
                
                foreach ($categoryModules as $module) {
                    $moduleData = [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description ?? null,
                        'ordre' => $module->position,
                        'diagnosticmoduletype_id' => $module->diagnosticmoduletype_id,
                        'diagnosticmodulecategory_id' => $module->diagnosticmodulecategory_id,
                        'entrepriseprofil_id' => $module->entrepriseprofil_id,
                        'questions' => []
                    ];

                    foreach ($module->questions as $question) {
                        $questionData = [
                            'id' => $question->id,
                            'titre' => $question->titre,
                            'description' => $question->description ?? null,
                            'ordre' => $question->position,
                            'obligatoire' => $question->obligatoire,
                            'type' => $question->diagnosticquestiontype->titre ?? null,
                            'categorie' => $question->diagnosticquestioncategorie->titre ?? null,
                            'reponses' => $question->reponses->map(function($reponse) {
                                return [
                                    'id' => $reponse->id,
                                    'titre' => $reponse->titre,
                                    'score' => $reponse->score,
                                    'ordre' => $reponse->position
                                ];
                            })->toArray()
                        ];
                        $moduleData['questions'][] = $questionData;
                    }
                    $categoryData['modules'][] = $moduleData;
                }
                $structure[] = $categoryData;
            }

            return response()->json([
                'success' => true,
                'message' => 'Structure du diagnostic par profil récupérée avec succès',
                'data' => [
                    'profil_id' => $profilId,
                    'categories' => $structure,
                    'total_categories' => count($structure),
                    'total_modules' => $modules->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la structure: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Récupère les types de modules disponibles
     * 
     * @return JsonResponse
     */
    public function getModuleTypes(): JsonResponse
    {
        try {
            $types = \App\Models\Diagnosticmoduletype::where('etat', 1)
                ->orderBy('titre', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Types de modules récupérés avec succès',
                'data' => $types
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des types: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Récupère les profils d'entreprise disponibles
     * 
     * @return JsonResponse
     */
    public function getEntrepriseProfils(): JsonResponse
    {
        try {
            $profils = \App\Models\Entrepriseprofil::where('etat', 1)
                ->orderBy('titre', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Profils d\'entreprise récupérés avec succès',
                'data' => $profils
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des profils: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
