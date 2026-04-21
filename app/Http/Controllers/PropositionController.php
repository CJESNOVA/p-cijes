<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use App\Models\Plan;
use App\Models\Expert;
use App\Models\Membre;
use App\Models\Prestation;
use App\Models\Propositionstatut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropositionController extends Controller
{
    /**
     * Afficher le formulaire de création de proposition
     */
    public function create(Plan $plan)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous devez avoir un profil expert pour faire une proposition.');
        }

        // Récupérer les statuts de proposition disponibles
        $statuts = Propositionstatut::where('etat', 1)->get();
        
        // Récupérer les prestations de l'expert connecté
        // L'expert est un membre qui peut être associé à une ou plusieurs entreprises
        $entreprisesIds = $membre->entreprisemembres()->pluck('entreprise_id');
        $prestations = Prestation::where('etat', 1)
            ->whereIn('entreprise_id', $entreprisesIds)
            ->get();

        return view('proposition.create', compact('plan', 'expert', 'statuts', 'prestations'));
    }

    /**
     * Afficher le formulaire de modification de proposition
     */
    public function edit(Proposition $proposition)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert || $proposition->expert_id !== $expert->id) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette proposition.');
        }

        // Récupérer les prestations de l'expert connecté
        $entreprisesIds = $membre->entreprisemembres()->pluck('entreprise_id');
        $prestations = Prestation::where('etat', 1)
            ->whereIn('entreprise_id', $entreprisesIds)
            ->get();

        return view('proposition.edit', compact('proposition', 'expert', 'prestations'));
    }

    /**
     * Enregistrer une nouvelle proposition
     */
    public function store(Request $request, Plan $plan)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous devez avoir un profil expert pour faire une proposition.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'prix_propose' => 'nullable|numeric|min:0|max:999999999.99',
            'duree_prevue' => 'nullable|integer|min:1|max:365',
            'date_expiration' => 'nullable|date|after:today',
            'prestation_id' => 'nullable|exists:prestations,id',
        ]);

        // Récupérer le statut "En attente" par défaut
        $statutEnAttente = Propositionstatut::where('titre', 'En attente')->first();

        Proposition::create([
            'membre_id' => $membre->id,
            'expert_id' => $expert->id,
            'prestation_id' => $request->prestation_id,
            'plan_id' => $request->plan_id,
            'accompagnement_id' => $request->accompagnement_id,
            'message' => $request->message,
            'prix_propose' => $request->prix_propose,
            'duree_prevue' => $request->duree_prevue,
            'propositionstatut_id' => $statutEnAttente->id ?? 1,
            'date_proposition' => now(),
            'date_expiration' => $request->date_expiration,
            'etat' => 1,
        ]);

        return redirect()->route('expert.plans.show', $request->plan_id)
            ->with('success', '✅ Votre proposition a été envoyée avec succès !');
    }

    /**
     * Afficher les propositions d'un expert
     */
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous devez avoir un profil expert pour voir vos propositions.');
        }

        $propositions = Proposition::with([
            'plan.accompagnement.entreprise.secteur',
            'plan.accompagnement.membre',
            'prestation',
            'statut'
        ])
        ->where('expert_id', $expert->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('proposition.index', compact('propositions', 'expert'));
    }

    /**
     * Afficher les détails d'une proposition
     */
    public function show(Proposition $proposition)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert || $proposition->expert_id !== $expert->id) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous n\'êtes pas autorisé à voir cette proposition.');
        }

        $proposition->load([
            'plan.accompagnement.entreprise.secteur',
            'plan.accompagnement.membre',
            'prestation',
            'statut'
        ]);

        return view('proposition.show', compact('proposition', 'expert'));
    }

    /**
     * Mettre à jour une proposition
     */
    public function update(Request $request, Proposition $proposition)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert || $proposition->expert_id !== $expert->id) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette proposition.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'prix_propose' => 'nullable|numeric|min:0|max:999999999.99',
            'duree_prevue' => 'nullable|integer|min:1|max:365',
            'date_expiration' => 'nullable|date|after:today',
            'prestation_id' => 'nullable|exists:prestations,id',
        ]);

        $proposition->update([
            'message' => $request->message,
            'prix_propose' => $request->prix_propose,
            'duree_prevue' => $request->duree_prevue,
            'date_expiration' => $request->date_expiration,
            'prestation_id' => $request->prestation_id,
        ]);

        return redirect()->route('proposition.show', $proposition->id)
            ->with('success', '✅ Proposition mise à jour avec succès !');
    }

    /**
     * Supprimer une proposition
     */
    public function destroy(Proposition $proposition)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        
        $expert = Expert::where('membre_id', $membre->id)->first();
        
        if (!$expert || $proposition->expert_id !== $expert->id) {
            return redirect()->route('expert.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette proposition.');
        }

        $proposition->delete();

        return redirect()->route('proposition.index')
            ->with('success', '🗑️ Proposition supprimée avec succès !');
    }
}
