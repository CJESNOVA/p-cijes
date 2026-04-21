<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    // Liste des forums du même pays que le membre connecté
    public function index()
{
    $userId = Auth::id();

    // Récupère le membre lié à cet utilisateur
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Récupère les forums du même pays ou globaux
    $forums = Forum::where('etat', 1)
        ->where(function ($q) use ($membre) {
            $q->whereNull('pays_id')
              ->orWhere('pays_id', $membre->pays_id);
        })
        ->orderByDesc('created_at')
        ->get();

    // Pour chaque forum, récupérer les sujets du membre connecté
    $forums->each(function($forum) use ($membre) {
        $forum->mesSujets = $forum->sujets()
            ->where('membre_id', $membre->id)
            ->orderByDesc('created_at')
            ->get();
    });

    return view('forum.index', compact('forums'));
}

}
