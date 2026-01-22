<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Expert;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertPlanController extends Controller
{
    /**
     * Afficher la liste des plans d'accompagnement pour les experts
     * avec tri par pertinence sectorielle
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        // Récupérer l'expert connecté
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous devez avoir un profil expert pour accéder à cette page.');
        }

        // Récupérer le secteur de l'expert
        $expertSecteurId = $expert->secteur_id;

        // Récupérer tous les plans actifs avec leurs relations
        $query = Plan::with([
            'accompagnement.entreprise.secteur',
            'accompagnement.membre'
        ])
        ->where('etat', true)
        ->whereHas('accompagnement', function($q) {
            $q->where('etat', true);
        })
        ->whereHas('accompagnement.entreprise', function($q) {
            $q->where('etat', true);
        });

        // Plans du même secteur que l'expert (prioritaires)
        $plansMemeSecteur = (clone $query)
            ->whereHas('accompagnement.entreprise.secteur', function($q) use ($expertSecteurId) {
                $q->where('id', $expertSecteurId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Plans d'autres secteurs
        $autresPlans = (clone $query)
            ->whereHas('accompagnement.entreprise.secteur', function($q) use ($expertSecteurId) {
                $q->where('id', '!=', $expertSecteurId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Fusionner avec ordre de priorité
        $plans = $plansMemeSecteur->concat($autresPlans);

        // Statistiques
        $stats = [
            'total_plans' => $plans->count(),
            'plans_meme_secteur' => $plansMemeSecteur->count(),
            'plans_autres_secteurs' => $autresPlans->count(),
        ];

        return view('expert.plans.index', compact('plans', 'expert', 'stats'));
    }

    /**
     * Afficher les détails d'un plan
     */
    public function show(Plan $plan)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous devez avoir un profil expert pour accéder à cette page.');
        }

        // Charger le plan avec toutes ses relations
        $plan->load([
            'accompagnement.entreprise.secteur',
            'accompagnement.membre',
            'accompagnement.diagnostics'
        ]);

        return view('expert.plans.show', compact('plan', 'expert'));
    }
}
