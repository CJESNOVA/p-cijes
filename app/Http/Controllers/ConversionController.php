<?php

namespace App\Http\Controllers;

use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Conversion;
use App\Models\Entreprisemembre;
use Illuminate\Http\Request;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ConversionController extends Controller
{
    /**
     * Liste des conversions liées au membre connecté.
     */
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        $conversions = Conversion::with([
            'ressourcetransactionsource.ressourcecompte',
            'ressourcetransactioncible.ressourcecompte'
        ])
        ->where(function ($q) use ($membre, $entrepriseIds) {
            $q->whereHas('ressourcetransactionsource.ressourcecompte', function ($sub) use ($membre, $entrepriseIds) {
                $sub->where('membre_id', $membre->id)
                    ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orWhereHas('ressourcetransactioncible.ressourcecompte', function ($sub) use ($membre, $entrepriseIds) {
                $sub->where('membre_id', $membre->id)
                    ->orWhereIn('entreprise_id', $entrepriseIds);
            });
        })
        ->orderByDesc('id')
        ->get();

        return view('conversion.index', compact('conversions'));
    }

    /**
     * Formulaire de conversion
     */
    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Comptes source (type 1)
        $comptesSource = Ressourcecompte::where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id)
                  ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->where('ressourcetype_id', 1)
            ->where('etat', 1)
            ->get();

        return view('conversion.create', compact('comptesSource'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'compte_source_id' => 'required|exists:ressourcecomptes,id',
            'montant'          => 'required|numeric|min:0.01',
        ]);

            $userId = Auth::id();
            $membre = Membre::where('user_id', $userId)->first();
            
        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id')
            ->toArray();

        $compteSource = Ressourcecompte::findOrFail($request->compte_source_id);
        $montant      = $request->montant;

        // Vérifier appartenance
        if (!($compteSource->membre_id == $membre->id || in_array($compteSource->entreprise_id, $entrepriseIds))) {
            return back()->with('error', '⚠️ Compte source invalide.');
        }

        if ($compteSource->solde < $montant) {
            return back()->with('error', '⚠️ Solde insuffisant sur le compte source.');
        }

        
            $compteCible = Ressourcecompte::firstOrCreate(
                [
                    'ressourcetype_id' => 3,
                    'membre_id'        => $membre->id,
                    'entreprise_id'    => $compteSource->entreprise_id, // ou null si pas d'entreprise
                ],
                [
                    'nom_complet' => 'Compte cible type 3 - ' . $membre->nom,
                    'solde'       => 0,
                    'etat'        => 1,
                    'spotlight'   => 0,
                ]
            );


        $taux = 1; // à adapter si besoin

        DB::beginTransaction();
        try {
            $reference = 'CONV-' . Str::upper(Str::random(8));

            // Débit compte source
            $txSource = Ressourcetransaction::create([
                'montant' => -$montant,
                'reference' => $reference,
                'ressourcecompte_id' => $compteSource->id,
                'datetransaction' => now(),
                'operationtype_id' => 2,
                'entreprise_id' => $compteSource->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            // Crédit compte cible
            $montantCible = $montant * $taux;
            $txCible = Ressourcetransaction::create([
                'montant' => $montantCible,
                'reference' => $reference,
                'ressourcecompte_id' => $compteCible->id,
                'datetransaction' => now(),
                'operationtype_id' => 1,
                'entreprise_id' => $compteCible->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            // Enregistrer la conversion
            Conversion::create([
                'taux' => $taux,
                'ressourcetransaction_source_id' => $txSource->id,
                'ressourcetransaction_cible_id' => $txCible->id,
                'membre_id' => $membre->id,
                'entreprise_id' => $compteSource->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            // Mettre à jour les soldes
            $compteSource->decrement('solde', $montant);
            $compteCible->increment('solde', $montantCible);

            DB::commit();

            return redirect()->route('conversion.index')
                ->with('success', "✅ Conversion effectuée : $montant → $montantCible (taux $taux)");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '⚠️ Erreur : ' . $e->getMessage());
        }
    }

}
