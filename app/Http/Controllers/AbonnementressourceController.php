<?php

namespace App\Http\Controllers;

use App\Models\Abonnementressource;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbonnementressourceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', 'Vous devez d\'abord créer votre profil membre.');
        }

        // Récupérer les abonnements payés du membre et de ses entreprises
        $abonnementressources = Abonnementressource::with([
            'abonnement.abonnementtype',
            'abonnement.entreprise',
            'membre',
            'entreprise',
            'ressourcecompte'
        ])
        ->where(function($query) use ($membre) {
            $query->where('membre_id', $membre->id)
                  ->orWhereHas('abonnement.entreprise.entreprisesmembres', function($q) use ($membre) {
                      $q->where('membre_id', $membre->id);
                  });
        })
        ->where('etat', 1)
        ->orderByDesc('created_at')
        ->get();

        return view('abonnementressource.index', compact('abonnementressources'));
    }

    public function show($id)
    {
        $abonnementressource = Abonnementressource::with([
            'abonnement.abonnementtype',
            'abonnement.entreprise',
            'membre',
            'entreprise',
            'ressourcecompte'
        ])->findOrFail($id);

        // Vérifier que l'utilisateur a le droit de voir cet abonnement
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre || ($abonnementressource->membre_id !== $membre->id && !$abonnementressource->abonnement->entreprise->entreprisesmembres()->where('membre_id', $membre->id)->exists())) {
            return redirect()->route('abonnementressource.index')
                ->with('error', 'Vous n\'avez pas les droits pour voir cet abonnement.');
        }

        return view('abonnementressource.show', compact('abonnementressource'));
    }
}
