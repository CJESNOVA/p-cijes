<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Formationniveau;
use App\Models\Formationtype;
use App\Models\Participant;
use App\Models\Participantstatut;
use App\Models\Membre;
use App\Models\Expert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $expert = Expert::where('membre_id', $membre->id)->first();
        $formations = Formation::where('expert_id', $expert->id)->orderByDesc('id')->get();

        return view('formation.index', compact('formations'));
    }

    public function create()
    {
        $formationniveaux = Formationniveau::where('etat', 1)->get();
        $formationtypex = Formationtype::where('etat', 1)->get();
        return view('formation.form', ['formation' => null, 'formationniveaux' => $formationniveaux, 'formationtypex' => $formationtypex]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $expert = Expert::where('membre_id', $membre->id)->first();

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'formationniveau_id' => 'required|exists:formationniveaus,id',
            'formationtype_id' => 'required|exists:formationtypes,id',
            'datedebut' => 'required|date',
            'datefin' => 'required|date|after_or_equal:datedebut',
            'description' => 'nullable|string',
        ]);

        $validated['expert_id'] = $expert->id;
        $validated['pays_id'] = $expert->pays_id;
        $validated['etat'] = 1;
        $validated['spotlight'] = 0;

        Formation::create($validated);

        return redirect()->route('formation.index')->with('success', 'Formation créée avec succès.');
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $expert = Expert::where('membre_id', $membre->id)->first();

        $formation = Formation::where('expert_id', $expert->id)->findOrFail($id);

        $formationniveaux = Formationniveau::where('etat', 1)->get();
        $formationtypex = Formationtype::where('etat', 1)->get();
        return view('formation.form', compact('formation', 'formationniveaux', 'formationtypex'));
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $expert = Expert::where('membre_id', $membre->id)->first();

        $formation = Formation::where('expert_id', $expert->id)->findOrFail($id);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'formationniveau_id' => 'required|exists:formationniveaus,id',
            'formationtype_id' => 'required|exists:formationtypes,id',
            'datedebut' => 'required|date',
            'datefin' => 'required|date|after_or_equal:datedebut',
            'description' => 'nullable|string',
        ]);

        $formation->update($validated);

        return redirect()->route('formation.index')->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $expert = Expert::where('membre_id', $membre->id)->first();

        $formation = Formation::where('expert_id', $expert->id)->findOrFail($id);

        $formation->delete();
        return redirect()->route('formation.index')->with('success', 'Formation supprimée.');
    }

    public function participants($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $expert = Expert::where('membre_id', $membre->id)->first();

        $formation = Formation::where('expert_id', $expert->id)->findOrFail($id);

        $participants = Participant::with(['membre', 'participantstatut'])
            ->where('formation_id', $formation->id)
            ->get();

        return view('formation.participants', compact('formation', 'participants'));
    }

    
    // Liste des formations ouvertes
    public function liste()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $expert = Expert::where('membre_id', $membre->id)->first();
        $membreId = $membre->id; // ID du membre connecté

        $formations = Formation::with(['participants', 'formationniveau', 'formationtype', 'expert.membre'])
            ->where('etat', 1)
            ->orderByDesc('id')->get();

        return view('formation.liste', compact('formations', 'expert', 'membreId'));
    }

    // Inscription d'un membre à une formation
    public function inscrire($id)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();
        $formation = Formation::where('etat', 1)->findOrFail($id);

        // Vérifier si déjà inscrit
        $existe = Participant::where('membre_id', $membre->id)
            ->where('formation_id', $formation->id)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Vous êtes déjà inscrit à cette formation.');
        }

        // Statut par défaut (ex: "En attente")
        $statutDefaut = Participantstatut::where('etat', 1)->first();
        $statutId = $statutDefaut ? $statutDefaut->id : null;

        Participant::create([
            'membre_id' => $membre->id,
            'formation_id' => $formation->id,
            'dateparticipant' => now(),
            'participantstatut_id' => $statutId,
            'etat' => 1,
            'spotlight' => 0,
        ]);

        return redirect()->back()->with('success', 'Inscription réussie.');
    }

}
