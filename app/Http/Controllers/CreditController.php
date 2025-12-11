<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Membre;
use App\Models\Entreprisemembre;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Récupérer toutes les entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Récupérer les crédits liés à ses entreprises
        $credits = Credit::with(['entreprise', 'credittype', 'creditstatut', 'partenaire', 'user'])
            ->whereIn('entreprise_id', $entrepriseIds)
            ->orderByDesc('id')
            ->get();

        return view('credit.index', compact('credits'));
    }
}
