<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Documenttype;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\SupabaseStorageService;

class DocumentController extends Controller
{
    public function indexForm()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        // S'il n'y a pas encore de membre, on prépare un objet "vide"
        if (!$membre) {
            $membre = new Membre(['user_id' => $userId]);
        }

        $documenttypes = Documenttype::where('etat', 1)->get();

        // Récupérer les documents existants de ce membre (ou vide si pas de membre)
        $documents = $membre->exists
            ? Document::where('membre_id', $membre->id)->get()->keyBy('documenttype_id')
            : collect();

        return view('document.form', compact('documenttypes', 'documents', 'membre'));
    }

    public function storeOrUpdateDocuments(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        // ⚠️ Si le membre n’existe pas encore, on bloque l’opération
        if (!$membre) {
            return redirect()->route('membre.createOrEdit')->with('error', 'Vous devez d\'abord créer votre profil membre avant de déposer vos documents.');
        }

        $documenttypes = Documenttype::where('etat', 1)->get();

        foreach ($documenttypes as $documenttype) {
            $inputName = 'document_' . $documenttype->id;

            if ($request->hasFile($inputName)) {
                //$fichier = $request->file($inputName)->store('documents', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file($inputName);
                $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
                $path = 'documents/' . time() . '_' . $cleanName;
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $fichier = $path;

                Document::updateOrCreate(
                    [
                        'membre_id' => $membre->id,
                        'documenttype_id' => $documenttype->id,
                    ],
                    [
                        'titre' => $documenttype->titre,
                        'fichier' => $fichier,
                        'datedocument' => now(),
                        'etat' => 1,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Documents enregistrés avec succès.');
    }

}
