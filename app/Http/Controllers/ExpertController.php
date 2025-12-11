<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\Membre;
use App\Models\Experttype;
use App\Models\Pays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $experts = Expert::where('membre_id', $membre->id)->get();

        return view('expert.index', compact('experts'));
    }


    public function liste()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Charger le pays depuis Supabase
        $paysService = new \App\Models\Pays();
        $pays = $paysService->find($membre->pays_id);

        // Liste des experts du même pays (en base Laravel)
        $experts = Expert::with(['experttype', 'membre', 'evaluations'])
            ->whereHas('membre', function ($q) use ($membre) {
                $q->where('pays_id', $membre->pays_id);
            })
            ->where('etat', 1)
            ->get();

        $myExperts = Expert::where('membre_id', $membre->id)->get();    

        return view('expert.liste', compact('experts', 'pays', 'myExperts'));
    }

    public function create()
    {
        $experttypes = Experttype::where('etat', 1)->get();
        return view('expert.form', ['expert' => new Expert(), 'experttypes' => $experttypes]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $request->validate([
            'domaine' => 'required|string|max:255',
            'experttype_id' => 'required|exists:experttypes,id',
            'fichier' => 'nullable|file|max:2048',
        ]);

        $path = $request->file('fichier')?->store('experts', 'public');

        Expert::create([
            'domaine' => $request->domaine,
            'experttype_id' => $request->experttype_id,
            'expertvalide_id' => 1,
            'membre_id' => $membre->id,
            'fichier' => $path,
            'etat' => 1,
        ]);

        return redirect()->route('expert.index')->with('success', '✅ Expert créé avec succès.');
    }

    public function edit(Expert $expert)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        if ($expert->membre_id !== $membre->id) {
            abort(403, 'Vous ne pouvez modifier que votre propre profil expert.');
        }

        $experttypes = Experttype::where('etat', 1)->get();
        return view('expert.form', compact('expert', 'experttypes'));
    }

    public function update(Request $request, Expert $expert)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        if ($expert->membre_id !== $membre->id) {
            abort(403, 'Action non autorisée.');
        }

        $request->validate([
            'domaine' => 'required|string|max:255',
            'experttype_id' => 'required|exists:experttypes,id',
            'fichier' => 'nullable|file|max:2048',
        ]);

        $path = $request->file('fichier')?->store('experts', 'public');

        $expert->update([
            'domaine' => $request->domaine,
            'experttype_id' => $request->experttype_id,
            'fichier' => $path ?? $expert->fichier,
        ]);

        return redirect()->route('expert.index')->with('success', '✅ Expert mis à jour.');
    }

    public function destroy(Expert $expert)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        if ($expert->membre_id !== $membre->id) {
            abort(403, 'Action non autorisée.');
        }

        $expert->delete();

        return redirect()->route('expert.index')->with('success', '🗑️ Expert supprimé.');
    }

    public function show(Expert $expert)
    {
        $expert->load(['membre', 'experttype', 'disponibilites.jour', 'evaluations.membre']);

        return view('expert.show', compact('expert'));
    }
}
