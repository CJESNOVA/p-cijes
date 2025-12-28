<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sujet;
use App\Models\Forum;
use App\Models\Membre;
use App\Models\Messageforum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\SupabaseStorageService;

class SujetController extends Controller
{
    /**
     * Liste des sujets d’un forum
     */

    public function index($forumId = null)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Si aucun forumId fourni, récupérer tous les sujets du membre
    if ($forumId) {
        $forum = Forum::findOrFail($forumId);

        $sujets = Sujet::where('forum_id', $forumId)
            ->where('membre_id', $membre->id)
            ->with('membre')
            ->orderByDesc('created_at')
            ->get();
    } else {
        $forum = null;

        $sujets = Sujet::where('membre_id', $membre->id)
            ->with('membre', 'forum') // charger le forum lié pour chaque sujet
            ->orderByDesc('created_at')
            ->get();
    }

    return view('sujet.index', compact('sujets', 'forum'));
}


    /**
     * Formulaire de création d’un sujet
     */
    public function create($forumId)
    {
        $forum = Forum::findOrFail($forumId);
        return view('sujet.form', ['sujet' => null, 'forum' => $forum]);
    }

    /**
     * Enregistrement d’un nouveau sujet
     */
    public function store(Request $request, $forumId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        $forum = Forum::findOrFail($forumId);

        $validated = $this->validateData($request);
        $validated['forum_id'] = $forum->id;
        $validated['membre_id'] = $membre->id;
        $validated['etat'] = 1;
        $validated['spotlight'] = $request->has('spotlight') ? 1 : 0;

        // Gestion du fichier ou texte de vignette
        if ($request->hasFile('vignette')) {
            //$validated['vignette'] = $request->file('vignette')->store('sujets', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file('vignette');
                $path = 'sujets/' . time() . '_' . $file->getClientOriginalName();
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $validated['vignette'] = $path;
        }

        Sujet::create($validated);

        return redirect()->route('sujet.index', $forum->id)
            ->with('success', 'Sujet créé avec succès.');
    }

    /**
     * Formulaire d’édition d’un sujet existant
     */
    public function edit($id)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();
        $sujet = Sujet::findOrFail($id);

        if ($sujet->membre_id !== $membre->id) {
            abort(403, 'Accès non autorisé.');
        }

        $forum = Forum::findOrFail($sujet->forum_id);
        return view('sujet.form', compact('sujet', 'forum'));
    }

    /**
     * Mise à jour d’un sujet existant
     */
    public function update(Request $request, $id)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();
        $sujet = Sujet::findOrFail($id);

        if ($sujet->membre_id !== $membre->id) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $this->validateData($request);
        $validated['spotlight'] = $request->has('spotlight') ? 1 : 0;

        if ($request->hasFile('vignette')) {
            // Supprimer ancienne vignette si existante
            /*if ($sujet->vignette && Storage::disk('public')->exists($sujet->vignette)) {
                Storage::disk('public')->delete($sujet->vignette);
            }*/
            //$validated['vignette'] = $request->file('vignette')->store('sujets', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file('vignette');
                $path = 'sujets/' . time() . '_' . $file->getClientOriginalName();
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $validated['vignette'] = $path;
        }

        $sujet->update($validated);

        return redirect()->route('sujet.index', $sujet->forum_id)
            ->with('success', 'Sujet mis à jour avec succès.');
    }

    /**
     * Suppression d’un sujet
     */
    public function destroy($id)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();
        $sujet = Sujet::findOrFail($id);

        if ($sujet->membre_id !== $membre->id) {
            abort(403, 'Accès non autorisé.');
        }

        if ($sujet->vignette && Storage::disk('public')->exists($sujet->vignette)) {
            Storage::disk('public')->delete($sujet->vignette);
        }

        $forumId = $sujet->forum_id;
        $sujet->delete();

        return redirect()->route('sujet.index', $forumId)
            ->with('success', 'Sujet supprimé avec succès.');
    }

    /**
     * Validation centralisée des données
     */
    private function validateData(Request $request)
    {
        return $request->validate([
            'titre' => 'required|string|max:255',
            'resume' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'vignette' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048', // ou texte
        ]);
    }

    public function liste()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $sujets = Sujet::with(['membre', 'forum'])
        ->whereHas('forum', function ($q) use ($membre) {
            $q->where('pays_id', $membre->pays_id); // sujets du même pays
        })
        ->orderByDesc('spotlight')      // d’abord les sujets en spotlight
        ->orderByDesc('created_at')     // puis par date de création
        ->get();

    return view('sujet.liste', compact('sujets'));
}


public function show($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $sujet = Sujet::with('membre', 'forum')->findOrFail($id);

    // Récupérer les messages du sujet, triés par spotlight puis date
    $messages = Messageforum::with('membre')
        ->where('sujet_id', $id)
        ->where('etat', 1)
        ->orderByDesc('spotlight')
        ->orderByDesc('created_at')
        ->paginate(20);

    return view('sujet.show', compact('sujet', 'messages', 'membre'));
}

public function storeMessage(Request $request, $sujetId)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $validated = $request->validate([
        'contenu' => 'required|string|max:2000',
    ]);

    Messageforum::create([
        'contenu' => $validated['contenu'],
        'sujet_id' => $sujetId,
        'membre_id' => $membre->id,
        'spotlight' => 0,
        'etat' => 1,
    ]);

    return redirect()->route('sujet.show', $sujetId)
        ->with('success', 'Message publié avec succès.');
}



}
