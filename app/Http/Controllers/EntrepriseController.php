<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entreprise;
use App\Models\Secteur;
use App\Models\Entreprisetype;
use App\Models\Entreprisemembre;
use App\Models\Pays;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\SupabaseStorageService;

class EntrepriseController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Récupérer le membre lié à ce user
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer les entreprises du membre
        $entreprises = Entreprisemembre::with(['entreprise', 'entreprise.entrepriseprofil'])
            ->where('membre_id', $membre->id)
            ->get();

        return view('entreprise.index', compact('entreprises'));
    }

    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')->with('error', 'Vous devez d\'abord créer votre profil membre.');
        }

        $secteurs = Secteur::where('etat', 1)->get();
        $entreprisetypes = Entreprisetype::where('etat', 1)->get();
        $payss = (new Pays())->all();

        return view('entreprise.form', [
            'entreprise' => null,
            'entreprisemembre' => null,
            'secteurs' => $secteurs,
            'entreprisetypes' => $entreprisetypes,
            'payss' => $payss,
        ]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        $entreprise = Entreprise::findOrFail($id);
        $entreprisemembre = Entreprisemembre::where('entreprise_id', $id)
            ->where('membre_id', $membre->id)
            ->first();

        $secteurs = Secteur::where('etat', 1)->get();
        $entreprisetypes = Entreprisetype::where('etat', 1)->get();
        $payss = (new Pays())->all();

        return view('entreprise.form', compact('entreprise', 'entreprisemembre', 'secteurs', 'entreprisetypes', 'payss'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')->with('error', 'Vous devez d\'abord créer votre profil membre.');
        }

        $validated = $this->validateData($request);

        // Vérifier si l'entreprise essaie d'être membre CJES sans être éligible
        $messageInfo = null;
        if (isset($validated['annee_creation']) && $validated['annee_creation']) {
            $anneeMin = date('Y') - 10;
            if ($validated['annee_creation'] < $anneeMin) {
                // Forcer à false si l'entreprise a plus de 10 ans
                if (isset($validated['est_membre_cijes']) && $validated['est_membre_cijes']) {
                    $messageInfo = "L'entreprise a été enregistrée mais ne peut pas être membre CJES car elle a plus de 10 ans.";
                }
                $validated['est_membre_cijes'] = 0;
            }
        }

        if ($request->hasFile('vignette')) {
            //$validated['vignette'] = $request->file('vignette')->store('entreprises', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file('vignette');
                $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
                $path = 'entreprises/' . time() . '_' . $cleanName;
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $validated['vignette'] = $path;
        }

        $entreprise = new Entreprise($validated);
        $entreprise->etat = 1;
        $entreprise->save();

        Entreprisemembre::create([
            'membre_id' => $membre->id,
            'entreprise_id' => $entreprise->id,
            'fonction' => $request->input('fonction', 'Fondateur'),
            'bio' => $request->input('bio', ''),
            'etat' => 1,
        ]);

        return redirect()->route('entreprise.index')->with('success', 'Entreprise créée avec succès.')->with('info', $messageInfo);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        $entreprise = Entreprise::findOrFail($id);

        $validated = $this->validateData($request);

        // Vérifier si l'entreprise essaie d'être membre CJES sans être éligible
        $messageInfo = null;
        if (isset($validated['annee_creation']) && $validated['annee_creation']) {
            $anneeMin = date('Y') - 10;
            if ($validated['annee_creation'] < $anneeMin) {
                // Forcer à false si l'entreprise a plus de 10 ans
                if (isset($validated['est_membre_cijes']) && $validated['est_membre_cijes']) {
                    $messageInfo = "L'entreprise a été mise à jour mais ne peut pas être membre CJES car elle a plus de 10 ans.";
                }
                $validated['est_membre_cijes'] = 0;
            }
        }

        if ($request->hasFile('vignette')) {
            //$validated['vignette'] = $request->file('vignette')->store('entreprises', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file('vignette');
                $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
                $path = 'entreprises/' . time() . '_' . $cleanName;
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $validated['vignette'] = $path;
        }

        $entreprise->update($validated);

        Entreprisemembre::updateOrCreate(
            ['membre_id' => $membre->id, 'entreprise_id' => $entreprise->id],
            ['fonction' => $request->fonction, 'bio' => $request->bio, 'etat' => 1]
        );

        return redirect()->route('entreprise.index')->with('success', 'Entreprise mise à jour avec succès.')->with('info', $messageInfo);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email',
            'telephone' => 'required|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'secteur_id' => 'required|exists:secteurs,id',
            'entreprisetype_id' => 'required|exists:entreprisetypes,id',
            'annee_creation' => 'nullable|integer|min:1900|max:' . date('Y'),
            'est_membre_cijes' => 'nullable|boolean',
            'pays_id' => 'required',
            'vignette' => 'nullable|image|max:2048',
            'fonction' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);
    }


    public function destroy($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Vérifie si le membre est bien lié à cette entreprise
    $entreprisemembre = Entreprisemembre::where('membre_id', $membre->id)
        ->where('entreprise_id', $id)
        ->first();

    if (!$entreprisemembre) {
        return redirect()
            ->route('entreprise.index')
            ->with('error', '❌ Vous n’êtes pas autorisé à supprimer cette entreprise.');
    }

    // Supprimer la relation membre-entreprise
    $entreprisemembre->delete();

    // Supprimer l’entreprise si plus aucun membre n’y est lié
    $autresMembres = Entreprisemembre::where('entreprise_id', $id)->count();

    if ($autresMembres === 0) {
        $entreprise = Entreprise::findOrFail($id);

        // Supprime la vignette du stockage si elle existe
        if ($entreprise->vignette && Storage::disk('public')->exists($entreprise->vignette)) {
            Storage::disk('public')->delete($entreprise->vignette);
        }

        $entreprise->delete();
    }

    return redirect()
        ->route('entreprise.index')
        ->with('success', '✅ Entreprise supprimée avec succès.');
}



}
