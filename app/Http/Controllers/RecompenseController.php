<?php

// app/Http/Controllers/RecompenseController.php
namespace App\Http\Controllers;

use App\Models\Recompense;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;

class RecompenseController extends Controller
{
    public function mesRecompenses()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        $recompenses = Recompense::where('membre_id', $membre->id)
            ->latest()
            ->get();

        return view('recompense.mesRecompenses', compact('recompenses'));
    }


    public function voir($id)
    {
        $alerte = \App\Models\Alerte::findOrFail($id);

        // Marquer comme lue
        if (!$alerte->lu) {
            $alerte->update(['lu' => 1]);
        }

        // Rediriger vers le lien prévu ou une page par défaut
        return redirect($alerte->lienurl ?? url('/'));
    }


}
