<?php

namespace App\Http\Controllers;

use App\Models\Piece;
use App\Models\Piecetype;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\SupabaseStorageService;

class PieceController extends Controller
{
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

        // ✅ Entreprises liées à ce membre
        $entrepriseMembres = Entreprisemembre::with('entreprise')
            ->where('membre_id', $membre->id)
            ->get();

        $entreprises = $entrepriseMembres->pluck('entreprise');

        // ✅ Types de pièces actifs
        $piecetypes = Piecetype::where('etat', 1)->get();

        // ✅ Toutes les pièces liées aux entreprises du membre
        $pieces = Piece::with(['entreprise', 'piecetype'])
            ->whereIn('entreprise_id', $entreprises->pluck('id'))
            ->get();

        // ✅ Indexer les dernières pièces par type (si besoin dans le formulaire)
        $piecesByType = $pieces->keyBy('piecetype_id');

        return view('piece.form', [
            'piecetypes'   => $piecetypes,
            'pieces'       => $pieces,       // 👉 pour lister toutes les pièces
            'piecesByType' => $piecesByType, // 👉 pour pré-remplir le formulaire
            'membre'       => $membre,
            'entreprises'  => $entreprises,
        ]);
    }

    public function storeOrUpdatePieces(Request $request)
    {
        // Récupérer le membre lié à l'utilisateur connecté
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // On récupère tous les types de pièces actifs
        $piecetypes = Piecetype::where('etat', 1)->get();

        [$fileRules, $fileMessages] = \App\Support\UploadLimit::dynamicFileRules($piecetypes, 'piece_');
        $request->validate($fileRules, $fileMessages);

        foreach ($piecetypes as $piecetype) {
            $inputName = 'piece_' . $piecetype->id;

            if ($request->hasFile($inputName)) {
                // Stockage du fichier dans le disque public sous le dossier pieces
                //$fichier = $request->file($inputName)->store('pieces', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file($inputName);
                $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
                $path = 'pieces/' . time() . '_' . $cleanName;
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $fichier = $path;

                // On met à jour ou crée une pièce suivant l'entreprise et le type
                Piece::updateOrCreate(
                    [
                        'entreprise_id' => $request->entreprise_id,
                        'piecetype_id' => $piecetype->id,
                    ],
                    [
                        'titre' => $piecetype->titre,
                        'fichier' => $fichier,
                        'datepiece' => now(),
                        'etat' => 1,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Pièces enregistrées avec succès.');
    }
}