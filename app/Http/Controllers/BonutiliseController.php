<?php

namespace App\Http\Controllers;

use App\Models\Bon;
use App\Models\Bonutilise;
use App\Models\Prestationrealisee;
use App\Models\Membre;
use App\Models\Entreprisemembre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonutiliseController extends Controller
{
    public function store(Request $request, Prestationrealisee $prestationrealisee)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'bon_id'      => 'required|exists:bons,id',
            'noteservice' => 'nullable|string|max:2000',
            'montant'     => 'nullable|numeric|min:0',
        ]);

        $bon = Bon::where('id', $validated['bon_id'])
            ->where('membre_id', $membre->id)
            ->firstOrFail();

        if ($bon->bonutilises()->exists()) {
            return back()->with('error', 'Ce bon a déjà été utilisé.');
        }

        $montant = $validated['montant'] ?? ($bon->valeur ?? $bon->montant ?? null);

        if (is_null($montant) || $montant <= 0) {
            return back()->with('error', "Montant invalide.");
        }

        Bonutilise::create([
            'montant'               => $montant,
            'noteservice'           => $validated['noteservice'] ?? null,
            'bon_id'                => $bon->id,
            'prestationrealisee_id' => $prestationrealisee->id,
            'etat'                  => 1,
            'spotlight'             => 0,
        ]);

        return redirect()
            ->route('bonutilise.show', $prestationrealisee->id)
            ->with('success', 'Bon utilisé avec succès.');
    }

    public function destroy(Bonutilise $bonutilise)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();

        if ($bonutilise->bon->membre_id !== $membre->id) {
            abort(403);
        }

        $prestationId = $bonutilise->prestationrealisee_id;
        $bonutilise->delete();

        return redirect()->route('bonutilise.show', $prestationId)
            ->with('success', 'Utilisation du bon annulée.');
    }
}
