<?php

namespace App\Http\Controllers;

use App\Models\Espaceressource;
use App\Models\Entreprisemembre;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;

class EspaceressourceController extends Controller
{
    /**
     * Liste des ressources utilisées pour payer des espaces
     * pour le membre connecté et ses entreprises
     */
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer les IDs des entreprises liées au membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id')
            ->toArray();

        // Récupérer toutes les espaces payées par le membre ou ses entreprises
        $ressources = Espaceressource::with([
                'ressourcecompte.membre',
                'ressourcecompte.entreprise',
                'espace',
                'paiementstatut'
            ])
            ->whereHas('ressourcecompte', function ($query) use ($membre, $entrepriseIds) {
                $query->where('membre_id', $membre->id)
                      ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        return view('espaceressource.index', compact('ressources'));
    }
}
