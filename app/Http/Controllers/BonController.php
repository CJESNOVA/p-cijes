<?php

namespace App\Http\Controllers;

use App\Models\Bon;
use App\Models\Membre;
use App\Models\Entreprisemembre;
use Illuminate\Support\Facades\Auth;

class BonController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Entreprises liées au membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Récupérer les bons liés à ces entreprises
        $bons = Bon::with(['entreprise', 'user', 'bontype', 'bonstatut'])
            ->whereIn('entreprise_id', $entrepriseIds)
            ->orderByDesc('datebon')
            ->get();

        return view('bon.index', compact('bons'));
    }
}
