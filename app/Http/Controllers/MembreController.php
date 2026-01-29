<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\Membretype;
use App\Models\Membrestatut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\SupabaseStorageService;

class MembreController extends Controller
{
    public function createOrEdit()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        $membretypes = Membretype::with('membrecategorie')->where('etat', 1)->get();
        $membrestatuts = Membrestatut::where('etat', 1)->get();

        // Appel à Supabase via ton service ou ton model personnalisé
        $paysModel = new \App\Models\Pays(); // s’il s’agit d’un model distant qui wrappe Supabase
        $payss = collect($paysModel->all())->keyBy('id');

        return view('membre.create-or-edit', compact('membre', 'membretypes', 'membrestatuts', 'payss'));
    }

    public function storeOrUpdate(Request $request, SupabaseStorageService $storage)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'membretype_id' => 'required|exists:membretypes,id',
            'pays_id' => 'required',
            //'membrestatut_id' => 'required|exists:membrestatuts,id',
            'vignette' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Le numero_identifiant sera généré automatiquement par le modèle dans la méthode boot

        $membre = Membre::updateOrCreate(
            ['user_id' => $userId],
            array_merge($validated, [
                'user_id' => $userId,
                'membrestatut_id' => 1,
                'etat' => 1,
                //'pays_id' => $request->pays_id ?? 1, // à adapter selon ton besoin
            ])
        );

        // Si image (vignette)
        /*if ($request->hasFile('vignette')) {
            $path = $request->file('vignette')->store('vignettes', 'public');
            $membre->vignette = $path;
            $membre->save();
        }*/

        if ($request->hasFile('vignette')) {
            $file = $request->file('vignette');
            $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
            $path = 'vignettes/' . time() . '_' . $cleanName;
            $url = $storage->upload($path, file_get_contents($file->getRealPath()));
            $membre->vignette = $path;
            $membre->save();
        }

        // Vérifier si c'est une création ou une modification
        $wasCreated = $membre->wasRecentlyCreated;

        if ($wasCreated) {
            // Rediriger vers le tableau de bord après la création du profil
            return redirect()->route('dashboard')
                ->with('success', 'Votre profil a été créé avec succès ! Bienvenue sur la plateforme e-CIJES.');
        } else {
            // Rediriger vers le tableau de bord après la modification du profil
            return redirect()->route('dashboard')
                ->with('success', 'Votre profil a été mis à jour avec succès !');
        }
    }
}
