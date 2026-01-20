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

class DiagnosticController extends Controller
{
    public function showForm()
{
        $userId = Auth::id();
        
    // Vérification du membre connecté
    $membre = Membre::where('user_id', $userId)->first();
    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Récupération du dernier diagnostic (le plus récent)
    $diagnostic = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', 0)
        ->where('diagnosticstatut_id', 1)
        ->where('diagnostictype_id', 1) 
        ->latest()
        ->first();

    // Récupération des modules et de leurs questions/réponses actives
    $diagnosticmodules = Diagnosticmodule::where([
            ['diagnosticmoduletype_id', 1],
            ['etat', 1],
        ])
        ->orderBy('position') // tri normal sur modules
        ->with([
            'diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                    ->orderBy('position') // tri normal sur questions
                    ->with(['diagnosticreponses' => function ($query) {
                        $query->inRandomOrder(); // tri aléatoire uniquement sur réponses
                    }]);
            },
        ])
        ->get();

    // Préparation des réponses déjà enregistrées (pour pré-cocher les réponses)
    $existing = [];
    if ($diagnostic) {
        $existing = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->get()
            ->groupBy('diagnosticquestion_id')
            ->map(fn($group) => $group->pluck('diagnosticreponse_id')->toArray());
    }

    // Retour de la vue
    return view('diagnostic.form', [
        'diagnosticmodules' => $diagnosticmodules,
        'existing' => $existing,
        'diagnostic' => $diagnostic,
        'membre' => $membre,
    ]);
}




    public function store(Request $request, RecompenseService $recompenseService)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->firstOrFail();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
    }

    $answers = $request->input('diagnosticreponses', []);

    // 🔍 Cherche un diagnostic EN COURS ou crée-en un nouveau si aucun n'existe
    $diagnostic = Diagnostic::where('membre_id', $membre->id)
        ->where('diagnosticstatut_id', 1) // 1 = en cours
        ->first();

    if (!$diagnostic) {
        $diagnostic = Diagnostic::create([
            'membre_id' => $membre->id,
            'diagnosticstatut_id' => 1,
            'diagnostictype_id' => 1,
            'scoreglobal' => 0,
            'etat' => 1,
        ]);
    }

    $totalScore = 0;

    foreach ($answers as $question_id => $values) {
        // 🧹 Supprimer les anciennes réponses de cette question
        Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->where('diagnosticquestion_id', $question_id)
            ->delete();

        // 📝 Enregistrer les nouvelles réponses
        if (is_array($values)) {
            foreach ($values as $reponse_id) {
                $reponse = Diagnosticreponse::find($reponse_id);
                $totalScore += $reponse?->score ?? 0;

                Diagnosticresultat::create([
                    'diagnostic_id' => $diagnostic->id,
                    'diagnosticquestion_id' => $question_id,
                    'diagnosticreponse_id' => $reponse_id,
                    'etat' => 1,
                ]);
            }
        } else {
            $reponse = Diagnosticreponse::find($values);
            $totalScore += $reponse?->score ?? 0;

            Diagnosticresultat::create([
                'diagnostic_id' => $diagnostic->id,
                'diagnosticquestion_id' => $question_id,
                'diagnosticreponse_id' => $values,
                'etat' => 1,
            ]);
        }
    }

    // ✅ Vérifier si toutes les questions obligatoires sont remplies
        // Récupérer tous les modules du diagnostic (type 1 pour PME)
        $modules = Diagnosticmodule::where('diagnosticmoduletype_id', 1)
            ->where('etat', 1)
            ->with(['diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                  ->where('obligatoire', 1);
            }])
            ->get();

        // Récupérer tous les IDs des questions obligatoires
        $obligatoires = $modules
            ->flatMap(function ($module) {
                return $module->diagnosticquestions->pluck('id');
            })
            ->unique()
            ->toArray();

        // Récupérer les questions obligatoires déjà répondues
        $repondues = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->whereIn('diagnosticquestion_id', $obligatoires)
            ->distinct('diagnosticquestion_id')
            ->pluck('diagnosticquestion_id')
            ->toArray();

        if (count($obligatoires) === count($repondues)) {
        // 💯 Diagnostic terminé
        $diagnostic->update([
            'scoreglobal' => $totalScore,
            'diagnosticstatut_id' => 2, // terminé
        ]);

        // 🏁 Création automatique d’un accompagnement
        $accompagnement = Accompagnement::create([
            'membre_id' => $membre->id,
            //'entreprise_id' => 0,
            'accompagnementniveau_id' => 1,
            'dateaccompagnement' => now(),
            'accompagnementstatut_id' => 1,
        ]);

        // 🔗 Lier le diagnostic à l’accompagnement
        $diagnostic->update([
            'accompagnement_id' => $accompagnement->id,
        ]);

        // 🎯 GÉNÉRATION AUTOMATIQUE DES PLANS D'ACCOMPAGNEMENT
        $this->genererPlansAutomatiques($diagnostic);

            // 🏆 Vérifie si c’est le premier diagnostic PME du membre
            $nbDiagnostics = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', 0)
                ->where('diagnosticstatut_id', 2)
                ->count();

            if ($nbDiagnostics === 1) {
                // 🪙 Déclenche la récompense "DIAG_PME_PREMIER"
                $recompense = $recompenseService->attribuerRecompense('DIAG_PME_PREMIER', $membre, null, $diagnostic->id);

            }

        return redirect()->route('diagnostic.success')
            ->with('success', 'Diagnostic terminé avec succès. Score : ' . $totalScore)
            ->with('diagnostic_id', $diagnostic->id);
    }

    return redirect()->back()
        ->with('success', 'Réponses enregistrées. Vous pouvez continuer plus tard.');
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
            // Calculer le niveau pour ce module
            $niveau = $this->calculerNiveauModule($diagnostic->id, $module->id);
            
            \Log::info('Niveau calculé pour module', [
                'module_id' => $module->id,
                'module_titre' => $module->titre,
                'niveau' => $niveau
            ]);
            
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

            \Log::info('Score module créé/mis à jour', [
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

            \Log::info('Templates trouvés', [
                'module_id' => $module->id,
                'niveau' => $niveau,
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
