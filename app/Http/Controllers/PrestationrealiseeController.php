<?php

namespace App\Http\Controllers;

use App\Models\Prestationrealisee;
use App\Models\Prestation;
use App\Models\Accompagnement;
use App\Models\Prestationrealiseestatut;
use Illuminate\Http\Request;

class PrestationrealiseeController extends Controller
{
    /**
     * Liste des prestations réalisées
     */
    public function index()
    {
        $prestationrealisees = Prestationrealisee::with(['prestation', 'accompagnement', 'prestationrealiseestatut'])->get();
        return view('prestationrealisees.index', compact('prestationrealisees'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $prestations = Prestation::all();
        $accompagnements = Accompagnement::all();
        $statuts = Prestationrealiseestatut::all();

        return view('prestationrealisees.create', compact('prestations', 'accompagnements', 'statuts'));
    }

    /**
     * Enregistrement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'note' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'prestation_id' => 'required|exists:prestations,id',
            'accompagnement_id' => 'nullable|exists:accompagnements,id',
            'daterealisation' => 'required|date',
            'prestationrealiseestatut_id' => 'required|exists:prestationrealiseestatuts,id',
        ]);

        Prestationrealisee::create($validated);

        return redirect()->route('prestationrealisees.index')->with('success', 'Prestation réalisée ajoutée avec succès.');
    }

    /**
     * Affichage d’un enregistrement
     */
    public function show(Prestationrealisee $prestationrealisee)
    {
        return view('prestationrealisees.show', compact('prestationrealisee'));
    }

    /**
     * Formulaire d’édition
     */
    public function edit(Prestationrealisee $prestationrealisee)
    {
        $prestations = Prestation::all();
        $accompagnements = Accompagnement::all();
        $statuts = Prestationrealiseestatut::all();

        return view('prestationrealisees.edit', compact('prestationrealisee', 'prestations', 'accompagnements', 'statuts'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, Prestationrealisee $prestationrealisee)
    {
        $validated = $request->validate([
            'note' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'prestation_id' => 'required|exists:prestations,id',
            'accompagnement_id' => 'nullable|exists:accompagnements,id',
            'daterealisation' => 'required|date',
            'prestationrealiseestatut_id' => 'required|exists:prestationrealiseestatuts,id',
        ]);

        $prestationrealisee->update($validated);

        return redirect()->route('prestationrealisees.index')->with('success', 'Prestation réalisée mise à jour avec succès.');
    }

    /**
     * Suppression
     */
    public function destroy(Prestationrealisee $prestationrealisee)
    {
        $prestationrealisee->delete();
        return redirect()->route('prestationrealisees.index')->with('success', 'Prestation réalisée supprimée avec succès.');
    }
}
