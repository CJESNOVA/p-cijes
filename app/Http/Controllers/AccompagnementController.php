<?php

namespace App\Http\Controllers;

use App\Models\Accompagnement;
use App\Models\Entreprisemembre;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;

class AccompagnementController extends Controller
{
    public function mesAccompagnements()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Les entreprises du membre connecté
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Accompagnements où le membre est directement l'accompagnateur
        // ou liés à ses entreprises
        $accompagnements = Accompagnement::with([
                'entreprise',
                'membre', // le conseiller
                'accompagnementniveau',
                'accompagnementstatut'
            ])
            ->where(function ($query) use ($membre, $entrepriseIds) {
                $query->where('membre_id', $membre->id)
                      ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        return view('accompagnement.index', compact('accompagnements'));
    }
}
