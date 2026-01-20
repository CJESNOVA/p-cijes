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
use App\Models\Accompagnement;

use App\Services\RecompenseService;

class DiagnosticentrepriseController extends Controller
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

        return view('diagnosticentreprise.choix_entreprise', compact('entreprises'));
    }
    
    public function showForm($entrepriseId)
    {
        // Récupération des modules type 2, triés par position
        $diagnosticmodules = Diagnosticmodule::where('diagnosticmoduletype_id', 2)
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

        // Diagnostic existant pour cette entreprise
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)
            ->where('diagnosticstatut_id', 1)
            ->where('diagnostictype_id', 2) 
            ->latest()
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
            'diagnosticmodules',
            'existing',
            'diagnostic',
            'entrepriseId'
        ));
    }


    public function store(Request $request, RecompenseService $recompenseService)
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

    $answers = $request->input('diagnosticreponses', []);
    $totalScore = 0;

    // Diagnostic unique par membre + entreprise
    $diagnostic = Diagnostic::firstOrCreate(
        [
            'entreprise_id' => $request->entreprise_id,
            'membre_id'     => $membre->id,
        ],
        [
            'diagnosticstatut_id' => 1,
            'diagnostictype_id'   => 2,
            'scoreglobal'         => 0,
            'etat'                => 1,
        ]
    );

    \DB::transaction(function () use ($answers, $diagnostic, &$totalScore) {
        foreach ($answers as $question_id => $values) {
            Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->where('diagnosticquestion_id', $question_id)
                ->delete();

            $values = is_array($values) ? $values : [$values];

            foreach ($values as $reponse_id) {
                $reponse = Diagnosticreponse::find($reponse_id);
                if ($reponse) {
                    $totalScore += $reponse->score ?? 0;

                    Diagnosticresultat::create([
                        'diagnostic_id'         => $diagnostic->id,
                        'diagnosticquestion_id' => $question_id,
                        'diagnosticreponse_id'  => $reponse_id,
                        'etat'                  => 1,
                    ]);
                }
            }
        }
    });

    // Modules d’évaluation
    $diagnosticmodules = Diagnosticmodule::where('diagnosticmoduletype_id', 2)
        ->where('etat', 1)
        ->orderBy('position')
        ->with(['diagnosticquestions' => function ($q) {
            $q->where('etat', 1)
              ->orderBy('position')
              ->with(['diagnosticreponses' => fn($query) => $query->where('etat', 1)]);
        }])
        ->get();

    // Questions obligatoires
    $obligatoires = $diagnosticmodules
        ->flatMap(fn($module) => $module->diagnosticquestions)
        ->where('obligatoire', 1)
        ->pluck('id')
        ->toArray();

    // Questions obligatoires répondues
    $repondues = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
        ->whereIn('diagnosticquestion_id', $obligatoires)
        ->distinct()
        ->pluck('diagnosticquestion_id')
        ->toArray();

    $allAnswered = empty(array_diff($obligatoires, $repondues));

    if ($allAnswered) {
        \DB::transaction(function () use ($diagnostic, $request, $membre, $totalScore) {

            // ✅ Met à jour le diagnostic
            $diagnostic->update([
                'scoreglobal'         => $totalScore,
                'diagnosticstatut_id' => 2,
            ]);

            // ✅ Vérifie / crée un accompagnement
            $accompagnement = Accompagnement::firstOrCreate(
                [
                    'entreprise_id' => $request->entreprise_id,
                    'membre_id'     => $membre->id,
                ],
                [
                    'accompagnementniveau_id' => 1,
                    'dateaccompagnement'      => now(),
                    'accompagnementstatut_id' => 1,
                ]
            );

            $diagnostic->update([
                'accompagnement_id' => $accompagnement->id,
            ]);

            // 🎯 GÉNÉRATION AUTOMATIQUE DES PLANS D'ACCOMPAGNEMENT
            $this->genererPlansAutomatiques($diagnostic);
        });

            // 🏆 Vérifie si c’est le premier diagnostic PME du membre
            $nbDiagnostics = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', $request->entreprise_id)
                ->where('diagnosticstatut_id', 2)
                ->count();

            if ($nbDiagnostics === 1) {
                
        $entreprise = Entreprise::findOrFail($request->entreprise_id);

                // 🪙 Déclenche la récompense "DIAG_ENTREPRISE_COMPLET"
                $recompense = $recompenseService->attribuerRecompense('DIAG_ENTREPRISE_COMPLET', $membre, $entreprise ?? null, $diagnostic->id);

            }

        return redirect()->route('diagnosticentreprise.success')
            ->with('success', "✅ Diagnostic terminé. Score : {$totalScore}")
            ->with('diagnostic_id', $diagnostic->id);
    }

    return redirect()->route('diagnosticentreprise.success')
        ->with('info', "🕓 Diagnostic partiellement rempli. Score actuel : {$totalScore}");
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

            // Récupérer tous les modules du diagnostic (type 2 pour entreprise)
            $modules = Diagnosticmodule::where('diagnosticmoduletype_id', 2)
                ->whereHas('diagnosticquestions', function($q) use ($diagnostic) {
                    $q->whereHas('diagnosticresultats', function($subQ) use ($diagnostic) {
                        $subQ->where('diagnostic_id', $diagnostic->id);
                    });
                })
                ->get();

            $plansCrees = 0;
            
            foreach ($modules as $module) {
                // Calculer le niveau pour ce module
                $niveau = $this->calculerNiveauModule($diagnostic->id, $module->id);
                
                // Créer ou mettre à jour le score du module
                $moduleScore = Diagnosticmodulescore::updateOrCreate(
                    [
                        'diagnostic_id' => $diagnostic->id,
                        'diagnosticmodule_id' => $module->id,
                    ],
                    [
                        'niveau' => $niveau,
                        'score_pourcentage' => $this->convertirNiveauEnPourcentage($niveau),
                        'score_max' => 100,
                        'score_total' => $this->convertirNiveauEnScore($niveau),
                    ]
                );

                \Log::info('Score module créé/mis à jour (entreprise)', [
                    'diagnostic_id' => $diagnostic->id,
                    'module_id' => $module->id,
                    'niveau' => $niveau,
                    'module_score_id' => $moduleScore->id,
                    'was_created' => $moduleScore->wasRecentlyCreated
                ]);

                // Chercher les templates correspondants
                $templates = Plantemplate::where('diagnosticmodule_id', $module->id)
                    ->where('niveau', $niveau)
                    ->actif()
                    ->get();

                \Log::info('Templates trouvés (entreprise)', [
                    'module_id' => $module->id,
                    'niveau' => $niveau,
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


}
