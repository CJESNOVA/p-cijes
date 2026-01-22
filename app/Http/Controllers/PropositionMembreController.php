<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use App\Models\Membre;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropositionMembreController extends Controller
{
    /**
     * Afficher les propositions reçues par un membre
     */
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        // Récupérer les plans du membre
        $plansIds = Plan::whereHas('accompagnement', function($query) use ($membre) {
            $query->where('membre_id', $membre->id);
        })->pluck('id');
        
        // Récupérer les propositions pour ces plans
        $propositions = Proposition::with([
            'expert.membre',
            'plan.accompagnement.entreprise',
            'prestation',
            'statut'
        ])
        ->whereIn('plan_id', $plansIds)
        ->orderBy('created_at', 'desc')
        ->get();
        
        return view('proposition.membre.index', compact('propositions', 'membre'));
    }
    
    /**
     * Afficher le détail d'une proposition reçue
     */
    public function show(Proposition $proposition)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        // Vérifier que la proposition est bien pour un plan du membre
        $plan = $proposition->plan;
        if (!$plan || $plan->accompagnement->membre_id !== $membre->id) {
            return redirect()->route('proposition.membre.index')
                ->with('error', 'Vous n\'êtes pas autorisé à voir cette proposition.');
        }
        
        $proposition->load([
            'expert.membre',
            'plan.accompagnement.entreprise',
            'prestation',
            'statut'
        ]);
        
        return view('proposition.membre.show', compact('proposition', 'membre'));
    }
    
    /**
     * Accepter une proposition
     */
    public function accepter(Proposition $proposition)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        // Vérifier que la proposition est bien pour un plan du membre
        $plan = $proposition->plan;
        if (!$plan || $plan->accompagnement->membre_id !== $membre->id) {
            return redirect()->route('proposition.membre.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette proposition.');
        }
        
        // Mettre à jour le statut
        $proposition->update([
            'propositionstatut_id' => 2, // "Acceptée"
        ]);
        
        return redirect()->route('proposition.membre.show', $proposition)
            ->with('success', '✅ Proposition acceptée avec succès !');
    }
    
    /**
     * Refuser une proposition
     */
    public function refuser(Proposition $proposition)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        // Vérifier que la proposition est bien pour un plan du membre
        $plan = $proposition->plan;
        if (!$plan || $plan->accompagnement->membre_id !== $membre->id) {
            return redirect()->route('proposition.membre.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette proposition.');
        }
        
        // Mettre à jour le statut
        $proposition->update([
            'propositionstatut_id' => 3, // "Refusée"
        ]);
        
        return redirect()->route('proposition.membre.show', $proposition)
            ->with('success', '✅ Proposition refusée avec succès !');
    }
}
