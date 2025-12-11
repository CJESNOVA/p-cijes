<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\Expert;
use App\Models\Disponibilite;
use App\Models\Jour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisponibiliteController extends Controller
{
    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Tous les experts (domaines) du membre connecté
        $experts = Expert::where('membre_id', $membre->id)->get();

        // Toutes les disponibilités liées à ses experts
        $disponibilites = Disponibilite::with('jour', 'expert')
            ->whereIn('expert_id', $experts->pluck('id'))
            ->orderBy('jour_id')
            ->orderBy('horairedebut')
            ->get();

        $jours = Jour::all();

        return view('disponibilite.create', compact('experts', 'disponibilites', 'jours'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $request->validate([
            'expert_id' => 'required|exists:experts,id',
            'horairedebut' => 'required',
            'horairefin' => 'required|after:horairedebut',
            'jour_id' => 'required|exists:jours,id',
        ]);

        // Vérifier que l’expert appartient bien au membre connecté
        $expert = Expert::where('id', $request->expert_id)
            ->where('membre_id', $membre->id)
            ->firstOrFail();

        // Vérifier si une disponibilité identique existe déjà
        $exists = Disponibilite::where('expert_id', $expert->id)
            ->where('jour_id', $request->jour_id)
            ->where('horairedebut', $request->horairedebut)
            ->where('horairefin', $request->horairefin)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', '⚠️ Cette disponibilité existe déjà pour ce domaine.');
        }

        Disponibilite::create([
            'expert_id' => $expert->id,
            'jour_id' => $request->jour_id,
            'horairedebut' => $request->horairedebut,
            'horairefin' => $request->horairefin,
            'etat' => 1,
        ]);

        return redirect()->back()->with('success', '✅ Disponibilité enregistrée avec succès.');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $disponibilite = Disponibilite::findOrFail($id);

        // Vérifier que la disponibilité appartient à l’un des experts du membre
        if ($disponibilite->expert->membre_id !== $membre->id) {
            abort(403, "Vous n’êtes pas autorisé à supprimer cette disponibilité.");
        }

        $disponibilite->delete();

        return redirect()->back()->with('success', '✅ Disponibilité supprimée avec succès.');
    }
}
