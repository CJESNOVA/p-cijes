<?php

namespace App\Http\Controllers;

use App\Models\Cotisationressource;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CotisationressourceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Récupérer les cotisations payées du membre et de ses entreprises
        $cotisationressources = Cotisationressource::with([
            'cotisation.cotisationtype',
            'cotisation.entreprise',
            'membre',
            'entreprise',
            'ressourcecompte'
        ])
        ->where(function($query) use ($membre) {
            $query->where('membre_id', $membre->id)
                  ->orWhereHas('cotisation.entreprise.entreprisesmembres', function($q) use ($membre) {
                      $q->where('membre_id', $membre->id);
                  });
        })
        ->where('etat', true)
        ->orderByDesc('created_at')
        ->get();

        return view('cotisationressource.index', compact('cotisationressources'));
    }

    public function show($id)
    {
        $cotisationressource = Cotisationressource::with([
            'cotisation.cotisationtype',
            'cotisation.entreprise',
            'membre',
            'entreprise',
            'ressourcecompte'
        ])->findOrFail($id);

        // Vérifier que l'utilisateur a le droit de voir cette cotisation
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre || ($cotisationressource->membre_id !== $membre->id && !$cotisationressource->cotisation->entreprise->entreprisesmembres()->where('membre_id', $membre->id)->exists())) {
            return redirect()->route('cotisationressource.index')
                ->with('error', 'Vous n\'avez pas les droits pour voir cette cotisation.');
        }

        return view('cotisationressource.show', compact('cotisationressource'));
    }
}
