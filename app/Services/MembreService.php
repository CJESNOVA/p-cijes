<?php

namespace App\Services;

use App\Models\Entreprisemembre;
use App\Models\Membre;
use App\Models\Ressourcecompte;
use Illuminate\Support\Facades\Auth;

class MembreService
{
    /**
     * Récupérer le membre authentifié ou null.
     *
     * Duplicated pattern across: PrestationController, EvenementController,
     * EspaceController, FormationController, and many others.
     */
    public function getAuthenticatedMembre(): ?Membre
    {
        return Membre::where('user_id', Auth::id())->first();
    }

    /**
     * Récupérer le membre authentifié ou lever une exception 404.
     */
    public function getAuthenticatedMembreOrFail(): Membre
    {
        return Membre::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Récupérer les IDs des entreprises liées au membre.
     *
     * Duplicated pattern across: PrestationController, EvenementController,
     * EspaceController, FormationController.
     */
    public function getEntrepriseIds(Membre $membre): array
    {
        return Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id')
            ->toArray();
    }

    /**
     * Récupérer les comptes ressources disponibles pour un membre et ses entreprises.
     *
     * Duplicated pattern across: PrestationController, EvenementController,
     * EspaceController, FormationController.
     */
    public function getRessourceComptes(Membre $membre, array $entrepriseIds)
    {
        return Ressourcecompte::where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id);
                if (!empty($entrepriseIds)) {
                    $q->orWhereIn('entreprise_id', $entrepriseIds);
                }
            })
            ->where('etat', 1)
            ->get();
    }
}
