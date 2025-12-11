<?php

namespace App\Http\Controllers;

use App\Models\Formationressource;
use App\Models\Entreprisemembre;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;

class FormationressourceController extends Controller
{
    /**
     * Liste des ressources utilisées pour payer des formations
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

        // Récupérer toutes les formations payées par le membre ou ses entreprises
        $ressources = Formationressource::with([
                'ressourcecompte.membre',
                'ressourcecompte.entreprise',
                'formation',
                'paiementstatut'
            ])
            ->whereHas('ressourcecompte', function ($query) use ($membre, $entrepriseIds) {
                $query->where('membre_id', $membre->id)
                      ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        return view('formationressource.index', compact('ressources'));
    }
}
