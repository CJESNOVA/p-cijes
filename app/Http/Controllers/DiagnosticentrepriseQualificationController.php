<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\Diagnosticmodule;
use App\Models\Diagnosticresultat;
use App\Models\Entreprisemembre;
use App\Models\Entreprise;
use App\Models\Membre;
use App\Services\RecompenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiagnosticentrepriseQualificationController extends Controller
{
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

        return view('diagnosticentreprisequalification.choix_entreprise', compact('entreprises'));
    }
    
    public function showForm($entrepriseId, $moduleId = null)
    {
        // Récupération de tous les modules type 3 (test de qualification), triés par position
        $allDiagnosticmodules = Diagnosticmodule::where('diagnosticmoduletype_id', 3)
            ->where('etat', 1)
            ->orderBy('position')
            ->with(['diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                ->orderBy('position')
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
        $currentModuleIndex = $allDiagnosticmodules->search(function($module) use ($moduleId) {
            return $module->id == $moduleId;
        });
        
        $nextModule = $allDiagnosticmodules->get($currentModuleIndex + 1);
        $previousModule = $currentModuleIndex > 0 ? $allDiagnosticmodules->get($currentModuleIndex - 1) : null;
        $isLastModule = ($currentModuleIndex + 1) >= $allDiagnosticmodules->count();


        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        // Récupérer le dernier diagnostic en cours pour cette entreprise (non terminé)
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id) 
            ->where('diagnostictype_id', 3) 
            ->where('diagnosticstatut_id', 1) // Non terminé
            ->orderBy('created_at', 'desc')
            ->first();

        /*// Si aucun diagnostic en cours, en créer un nouveau
        if (!$diagnostic) {
            $userId = Auth::id();
            $membre = Membre::where('user_id', $userId)->first();
            
            $diagnostic = Diagnostic::create([
                'entreprise_id' => $entrepriseId,
                'membre_id' => $membre->id,
                'diagnosticstatut_id' => 1,
                'diagnostictype_id' => 3,
                'scoreglobal' => 0,
                'etat' => 1,
            ]);
        }*/

        // Préparer les réponses existantes (déjà cochées)
        $existing = [];
        if ($diagnostic) {
            $existing = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->get()
                ->groupBy('diagnosticquestion_id')
                ->map(fn($items) => $items->pluck('diagnosticreponse_id')->toArray())
                ->toArray(); // convertir en array pour Blade
        }

        return view('diagnosticentreprisequalification.form', compact(
            'modules',
            'currentModule',
            'nextModule',
            'previousModule',
            'isLastModule',
            'existing',
            'diagnostic',
            'entrepriseId'
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

        $request->validate([
            'entreprise_id' => 'nullable|exists:entreprises,id',
        ]);

        // Récupérer le dernier diagnostic en cours pour cette entreprise (non terminé)
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id) 
            ->where('diagnostictype_id', 3) 
            ->where('diagnosticstatut_id', 1) // Non terminé
            ->orderBy('created_at', 'desc')
            ->first();

        // Si aucun diagnostic en cours, en créer un nouveau
        if (!$diagnostic) {
            $userId = Auth::id();
            $membre = Membre::where('user_id', $userId)->first();
            
            $diagnostic = Diagnostic::create([
                'entreprise_id' => $entrepriseId,
                'membre_id' => $membre->id,
                'diagnosticstatut_id' => 1,
                'diagnostictype_id' => 3,
                'scoreglobal' => 0,
                'etat' => 1,
            ]);
        }
            
        // Supprimer les anciens résultats pour ce module uniquement
        $moduleQuestionIds = Diagnosticmodule::find($moduleId)
            ->diagnosticquestions()
            ->pluck('id')
            ->toArray();
            
        Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->whereIn('diagnosticquestion_id', $moduleQuestionIds)
            ->delete();

        // Enregistrer les nouvelles réponses pour ce module
        foreach ($request->reponses as $questionId => $reponseData) {
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

        // Récupérer tous les modules pour trouver le suivant
        $allModules = Diagnosticmodule::where('diagnosticmoduletype_id', 3)
            ->where('etat', 1)
            ->orderBy('position')
            ->get();
        
        $currentModuleIndex = $allModules->search(function($module) use ($moduleId) {
            return $module->id == $moduleId;
        });
        
        $nextModule = $allModules->get($currentModuleIndex + 1);

        // Rediriger vers le module suivant ou rester sur le dernier
        if ($nextModule) {
            return redirect()->route('diagnosticentreprisequalification.showModule', [$entrepriseId, $nextModule->id])
                ->with('success', 'Module enregistré avec succès !');
        } else {
            return redirect()->back()->with('success', 'Dernier module enregistré avec succès !');
        }
    }

    public function store(Request $request, $entrepriseId, $moduleId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
        }

        $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
        ]);

        // Récupérer le dernier diagnostic en cours pour cette entreprise
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id) 
            ->where('diagnostictype_id', 3) 
            ->where('diagnosticstatut_id', 1) // Non terminé
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$diagnostic) {
            return redirect()->back()->with('error', 'Aucun diagnostic en cours trouvé.');
        }

        // Sauvegarder les réponses du dernier module
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
            foreach ($request->reponses as $questionId => $reponseData) {
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

        // Mettre à jour le diagnostic comme terminé
        $diagnostic->update([
            'diagnosticstatut_id' => 2, // Terminé
        ]);

        return redirect()->route('diagnosticentreprisequalification.success')
            ->with('success', 'Test de qualification enregistré avec succès !')
            ->with('entreprise', Entreprise::with('entrepriseprofil')->find($entrepriseId))
            ->with('diagnostic', $diagnostic);
    }

    public function success()
    {
        // Récupérer les données depuis la session
        $entreprise = session('entreprise');
        $diagnostic = session('diagnostic');
        $success = session('success');

        // Calculer les scores selon le nouveau système A,B,C
        $countA = 0;
        $countB = 0;
        $countC = 0;
        $profil = 0;
        $reponseMajoritaire = '';

        if ($diagnostic) {
            // Récupérer tous les résultats avec les détails
            $resultats = \App\Models\Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->with(['diagnosticquestion', 'diagnosticreponse'])
                ->get();

            // Compter les réponses par type (A,B,C)
            foreach ($resultats as $resultat) {
                $score = $resultat->diagnosticreponse->score ?? '';
                if ($score === 'A') {
                    $countA++;
                } elseif ($score === 'B') {
                    $countB++;
                } elseif ($score === 'C') {
                    $countC++;
                }
            }

            // Déterminer la réponse majoritaire et le profil correspondant
            if ($countA > $countB && $countA > $countC) {
                $profil = 1;
                $reponseMajoritaire = 'A';
            } elseif ($countB > $countA && $countB > $countC) {
                $profil = 2;
                $reponseMajoritaire = 'B';
            } elseif ($countC > $countA && $countC > $countB) {
                $profil = 3;
                $reponseMajoritaire = 'C';
            } else {
                // En cas d'égalité, on peut choisir la réponse la plus haute ou une logique spécifique
                if ($countC >= $countB && $countC >= $countA) {
                    $profil = 3;
                    $reponseMajoritaire = 'C';
                } elseif ($countB >= $countA && $countB >= $countC) {
                    $profil = 2;
                    $reponseMajoritaire = 'B';
                } else {
                    $profil = 1;
                    $reponseMajoritaire = 'A';
                }
            }
            
            // Mettre à jour le diagnostic avec la réponse majoritaire
            $diagnostic->update([
                'scoreglobal'         => $reponseMajoritaire,
                'diagnosticstatut_id' => 2,
            ]);

            // Mettre à jour l'entreprise avec le profil calculé
            if ($entreprise) {
                $entreprise->update(['entrepriseprofil_id' => $profil]);
            }
        }

        return view('diagnosticentreprisequalification.success', compact(
            'entreprise',
            'diagnostic',
            'success',
            'countA',
            'countB',
            'countC',
            'reponseMajoritaire',
            'profil'
        ));
    }

    public function results($entrepriseId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de voir les résultats.');
        }

        // Récupérer le dernier diagnostic terminé pour cette entreprise
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id) 
            ->where('diagnostictype_id', 3) 
            ->where('diagnosticstatut_id', 2) // Terminé
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$diagnostic) {
            return redirect()->back()->with('error', 'Aucun test de qualification terminé trouvé pour cette entreprise.');
        }

        // Récupérer l'entreprise avec son profil
        $entreprise = Entreprise::with('entrepriseprofil')->find($entrepriseId);

        // Calculer les scores selon le nouveau système A,B,C
        $countA = 0;
        $countB = 0;
        $countC = 0;
        $profil = 0;
        $reponseMajoritaire = '';

        // Récupérer tous les résultats avec les détails
        $resultats = \App\Models\Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->with(['diagnosticquestion', 'diagnosticreponse'])
            ->get();

        // Compter les réponses par type (A,B,C)
        foreach ($resultats as $resultat) {
            $score = $resultat->diagnosticreponse->score ?? '';
            if ($score === 'A') {
                $countA++;
            } elseif ($score === 'B') {
                $countB++;
            } elseif ($score === 'C') {
                $countC++;
            }
        }

        // Déterminer la réponse majoritaire et le profil correspondant
        if ($countA > $countB && $countA > $countC) {
            $profil = 1;
            $reponseMajoritaire = 'A';
        } elseif ($countB > $countA && $countB > $countC) {
            $profil = 2;
            $reponseMajoritaire = 'B';
        } elseif ($countC > $countA && $countC > $countB) {
            $profil = 3;
            $reponseMajoritaire = 'C';
        } else {
            // En cas d'égalité, on peut choisir la réponse la plus haute ou une logique spécifique
            if ($countC >= $countB && $countC >= $countA) {
                $profil = 3;
                $reponseMajoritaire = 'C';
            } elseif ($countB >= $countA && $countB >= $countC) {
                $profil = 2;
                $reponseMajoritaire = 'B';
            } else {
                $profil = 1;
                $reponseMajoritaire = 'A';
            }
        }

        return view('diagnosticentreprisequalification.results', compact(
            'entreprise',
            'diagnostic',
            'resultats',
            'countA',
            'countB',
            'countC',
            'reponseMajoritaire',
            'profil'
        ));
    }
}
