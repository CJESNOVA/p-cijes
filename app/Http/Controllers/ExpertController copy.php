<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\Membre;
use App\Models\Experttype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertController extends Controller
{
    public function create()
    {
        $experttypes = Experttype::where('etat', 1)->get();
        return view('expert.create', compact('experttypes'));
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

        $path = $request->file('fichier')?->store('experts');

        Expert::create([
            'domaine' => $request->domaine,
            'experttype_id' => $request->experttype_id,
            'expertvalide_id' => 1, // validation par défaut
            'membre_id' => $membre->id,
            'fichier' => $path,
            'etat' => 1,
        ]);

        return redirect()->route('expert.index')->with('success', 'Vous êtes désormais enregistré comme expert.');
    }

    public function index()
    {
        $experts = Expert::with('experttype', 'membre')->where('etat', 1)->get();
        return view('expert.index', compact('experts'));
    }

    public function show(Expert $expert)
    {
        $expert->load(['membre', 'experttype', 'disponibilites.jour', 'evaluations.membre']);

        return view('expert.show', compact('expert'));
    }

}
