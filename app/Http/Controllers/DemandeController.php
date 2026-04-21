<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Demandetype;
use App\Models\Membre;
use App\Models\Ressourcecompte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\SupabaseStorageService;

class DemandeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Vérifier si le membre existe
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', 'Vous devez d\'abord créer votre profil membre.');
        }

        // Récupérer les ressources du membre
        $ressourcecomptes = Ressourcecompte::where('membre_id', $membre->id)->get();

        // Toutes les demandes du membre avec leurs types
        $demandes = Demande::with(['demandetype', 'ressourcecompte'])
            ->whereIn('ressourcecompte_id', $ressourcecomptes->pluck('id'))
            ->orderBy('datedemande', 'desc')
            ->get();

        return view('demande.index', compact('demandes', 'membre'));
    }

    public function indexForm()
    {
        $userId = Auth::id();

        // ✅ Vérifier si le membre existe
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        $ressourcecomptes = Ressourcecompte::where('membre_id', $membre->id)->get();

        // ✅ Types de pièces actifs
        $demandetypes = Demandetype::where('etat', 1)->get();

        // ✅ Toutes les pièces liées aux ressourcecomptes du membre
        $demandes = Demande::with(['ressourcecompte', 'demandetype'])
            ->whereIn('ressourcecompte_id', $ressourcecomptes->pluck('id'))
            ->get();

        // ✅ Indexer les dernières pièces par type (si besoin dans le formulaire)
        $demandesByType = $demandes->keyBy('demandetype_id');

        return view('demande.form', [
            'demandetypes'   => $demandetypes,
            'demandes'       => $demandes,       // 👉 pour lister toutes les pièces
            'demandesByType' => $demandesByType, // 👉 pour pré-remplir le formulaire
            'membre'       => $membre,
            'ressourcecomptes'  => $ressourcecomptes,
        ]);
    }

    public function storeOrUpdateDemandes(Request $request)
    {
        // Récupérer le membre lié à l'utilisateur connecté
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // On récupère tous les types de pièces actifs
        $demandetypes = Demandetype::where('etat', 1)->get();

        foreach ($demandetypes as $demandetype) {
            $inputName = 'demande_' . $demandetype->id;

            if ($request->hasFile($inputName)) {
                // Stockage du fichier dans le disque public sous le dossier demandes
                //$fichier = $request->file($inputName)->store('demandes', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file($inputName);
                $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
                $path = 'demandes/' . time() . '_' . $cleanName;
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $fichier = $path;

                // On met à jour ou crée une pièce suivant l'ressourcecompte et le type
                Demande::updateOrCreate(
                    [
                        'ressourcecompte_id' => $request->ressourcecompte_id,
                        'demandetype_id' => $demandetype->id,
                    ],
                    [
                        'titre' => $demandetype->titre,
                        'fichier' => $fichier,
                        'datedemande' => now(),
                        'etat' => 1,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Pièces enregistrées avec succès.');
    }

    public function download($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            abort(403, 'Non autorisé');
        }

        $demande = Demande::with(['ressourcecompte' => function($q) use ($membre) {
            $q->where('membre_id', $membre->id);
        }])->findOrFail($id);

        if (!$demande->fichier) {
            abort(404, 'Fichier non trouvé');
        }

        // Vérifier que la demande appartient bien au membre
        if (!$demande->ressourcecompte || $demande->ressourcecompte->membre_id !== $membre->id) {
            abort(403, 'Non autorisé');
        }

        try {
            $storage = new \App\Services\SupabaseStorageService();
            $fileContent = $storage->download($demande->fichier);
            
            $fileName = basename($demande->fichier);
            
            return response($fileContent)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du téléchargement du fichier.');
        }
    }
}