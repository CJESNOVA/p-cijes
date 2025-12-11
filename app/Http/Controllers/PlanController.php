<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Accompagnement;
use App\Models\Membre;
use App\Models\Entreprisemembre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        $plans = Plan::with('accompagnement.entreprise', 'accompagnement.accompagnementniveau')
            ->whereHas('accompagnement', function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id)
                  ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        return view('plan.index', compact('plans'));
    }

    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Les entreprises du membre connecté
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Accompagnements où le membre est directement l'accompagnateur
        // ou liés à ses entreprises
        $accompagnements = Accompagnement::with([
                'entreprise',
                'membre', // le conseiller
                'accompagnementniveau',
                'accompagnementstatut'
            ])
            ->where(function ($query) use ($membre, $entrepriseIds) {
                $query->where('membre_id', $membre->id)
                      ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        return view('plan.form', [
            'plan' => new Plan(),
            'accompagnements' => $accompagnements
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'objectif' => 'required|string|max:255',
            'actionprioritaire' => 'required|string|max:255',
            'dateplan' => 'required|date',
            'accompagnement_id' => 'required|exists:accompagnements,id',
        ]);

        $validated['etat'] = 1;
        $validated['spotlight'] = 0;

        Plan::create($validated);

        return redirect()->route('plan.index')->with('success', 'Plan créé avec succès.');
    }

    public function edit(Plan $plan)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        $accompagnements = Accompagnement::with(['entreprise', 'accompagnementniveau'])
            ->where(function ($query) use ($membre, $entrepriseIds) {
                $query->where('membre_id', $membre->id)
                      ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('dateaccompagnement')
            ->get();

        return view('plan.form', compact('plan', 'accompagnements'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'objectif' => 'required|string|max:255',
            'actionprioritaire' => 'required|string|max:255',
            'dateplan' => 'required|date',
            'accompagnement_id' => 'required|exists:accompagnements,id',
        ]);

        $plan->update($validated);

        return redirect()->route('plan.index')->with('success', 'Plan modifié avec succès.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('plan.index')->with('success', 'Plan supprimé avec succès.');
    }

    public function createFromAccompagnement(Accompagnement $accompagnement)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Vérifier que l’accompagnement est bien lié au membre ou à ses entreprises
        if (!($accompagnement->membre_id == $membre->id || in_array($accompagnement->entreprise_id, $entrepriseIds->toArray()))) {
            abort(403);
        }

        $plan = new Plan();
        $plan->accompagnement_id = $accompagnement->id;

        return view('plan.form', [
            'plan' => $plan,
            'accompagnements' => collect([$accompagnement])
        ]);
    }
}
