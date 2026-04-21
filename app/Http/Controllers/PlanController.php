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

    /**
     * Stocke un nouveau plan depuis une modal (retour JSON)
     */
    public function storeFromModal(Request $request)
    {
        try {
            \Log::info('Début storeFromModal', [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            $validated = $request->validate([
                'objectif' => 'required|string|max:255',
                'actionprioritaire' => 'required|string|max:255',
                'dateplan' => 'required|date',
                'accompagnement_id' => 'required|exists:accompagnements,id',
            ]);

            \Log::info('Données validées', $validated);

            // Vérifier que l'utilisateur a le droit de créer un plan pour cet accompagnement
            $userId = Auth::id();
            $membre = Membre::where('user_id', $userId)->firstOrFail();
            
            $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
                ->pluck('entreprise_id')
                ->toArray();

            \Log::info('Membre et entreprises', [
                'membre_id' => $membre->id,
                'entreprise_ids' => $entrepriseIds
            ]);

            $accompagnement = Accompagnement::where('id', $validated['accompagnement_id'])
                ->where(function ($query) use ($membre, $entrepriseIds) {
                    $query->where('membre_id', $membre->id)
                          ->orWhereIn('entreprise_id', $entrepriseIds);
                })
                ->first();

            \Log::info('Accompagnement trouvé', [
                'accompagnement_id' => $validated['accompagnement_id'],
                'accompagnement' => $accompagnement,
                'accompagnement_membre_id' => $accompagnement->membre_id ?? 'NULL',
                'accompagnement_entreprise_id' => $accompagnement->entreprise_id ?? 'NULL'
            ]);

            if (!$accompagnement) {
                \Log::error('Accompagnement non autorisé', [
                    'accompagnement_id' => $validated['accompagnement_id'],
                    'membre_id' => $membre->id,
                    'entreprise_ids' => $entrepriseIds
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Accompagnement non trouvé ou non autorisé.'
                ], 403);
            }

            $validated['etat'] = 1;
            $validated['spotlight'] = 0;

            $plan = Plan::create($validated);
            \Log::info('Plan créé avec succès', ['plan' => $plan]);

            return response()->json([
                'success' => true,
                'message' => 'Plan créé avec succès.',
                'plan' => $plan
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation: ' . implode(', ', $e->errors()->all())
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du plan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du plan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testStore()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        
        if (!$membre) {
            return "Membre non trouvé";
        }
        
        // Récupérer les entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id')
            ->toArray();
        
        // Chercher un accompagnement (membre ou entreprise)
        $accompagnement = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entrepriseIds)
            ->first();
        
        if (!$accompagnement) {
            return "Aucun accompagnement trouvé pour le membre ID: " . $membre->id . " | Entreprises: " . implode(', ', $entrepriseIds);
        }
        
        try {
            $plan = Plan::create([
                'objectif' => 'Test objectif - ' . now(),
                'actionprioritaire' => 'Test action - ' . now(),
                'dateplan' => now()->addDays(7),
                'accompagnement_id' => $accompagnement->id,
                'etat' =>1,
                'spotlight' => 0,
            ]);
            
            return "Plan créé avec succès! ID: " . $plan->id . " | Accompanement ID: " . $accompagnement->id . " | Membre ID: " . $accompagnement->membre_id . " | Entreprise ID: " . ($accompagnement->entreprise_id ?? 'NULL');
        } catch (\Exception $e) {
            return "Erreur: " . $e->getMessage();
        }
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
