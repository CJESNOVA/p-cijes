<?php

namespace App\Http\Controllers;

use App\Models\Parrainage;
use App\Models\Membre;
use App\Models\User;
use App\Models\Ressourcecompte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\Services\RecompenseService;


class ParrainageController extends Controller
{
    /**
     * Liste des parrainages en attente pour le parrain
     */
    public function index()
    {
        $userId = Auth::id();
        $parrain = Membre::where('user_id', $userId)->first();

        $parrainages = Parrainage::with('membrefilleul')
            ->where('membre_parrain_id', $parrain->id)
            //->where('etat', 0)
            ->orderByDesc('id')
            ->get();

        $parrainages2 = Parrainage::with('membreparrain')
            ->where('membre_filleul_id', $parrain->id)
            //->where('etat', 0)
            ->orderByDesc('id')
            ->get();

        return view('parrainage.index', compact('parrainages', 'parrainages2'));
    }

    /**
     * Formulaire pour le filleul
     */
    public function create()
    {
        return view('parrainage.create');
    }

    /**
     * Stocker le parrainage
     */
    public function store(Request $request)
    {
        $request->validate([
            'email_parrain' => 'required|email|exists:membres,email',
        ]);

        $userId = Auth::id();
        $filleul = Membre::where('user_id', $userId)->first();

        $parrainMembre = Membre::where('email', $request->email_parrain)->first();
        $parrain = $parrainMembre ?? null;

        if (!$parrain) {
            return back()->with('error', '⚠️ Aucun membre trouvé avec cet email.');
        }

        if (Parrainage::where('membre_filleul_id', $filleul->id)->exists()) {
            return back()->with('error', '⚠️ Vous avez déjà un parrain.');
        }

        Parrainage::create([
            'membre_parrain_id' => $parrain->id,
            'membre_filleul_id' => $filleul->id,
            'etat' => 0,
            'spotlight' => 0,
        ]);

        return redirect()->route('parrainage.create')
            ->with('success', '✅ Parrainage envoyé, en attente de validation.');
    }



public function activer($id, RecompenseService $recompenseService)
{
    $userId = Auth::id();
    $parrain = Membre::where('user_id', $userId)->first();

    $parrainage = Parrainage::where('id', $id)
        ->where('membre_parrain_id', $parrain->id)
        ->where('etat', 0)
        ->firstOrFail();


    DB::beginTransaction();

    try {
        // 1. Valider le parrainage
        $parrainage->etat = 1;
        $parrainage->save();

        //dd($parrainage);
        // 2. Récompense pour le Parrain (Bon + Coris)
        $recompense_parrain = $recompenseService->attribuerRecompense('PARRAINAGE_PARRAIN', $parrain, null, $parrainage->id);

        // 3. Récompense pour le Filleul (Coris uniquement)
        $filleul = $parrainage->membrefilleul; // relation ->belongsTo(Membre::class, 'membre_filleul_id')
        $recompense_filleul = $recompenseService->attribuerRecompense('PARRAINAGE_FILLEUL', $filleul, null, $parrainage->id);

        if ($recompense_parrain && $recompense_filleul) {

        //dd($recompense_parrain);
        DB::commit();
        return back()->with('success', '✅ Parrainage activé : récompenses attribuées au parrain et au filleul.');
        }else{
        DB::rollBack();
        //dd($recompense_parrain);
        return back()->with('error', 'Récompense déjà attribée ...');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', '⚠️ Erreur : ' . $e->getMessage());
    }
}



}
