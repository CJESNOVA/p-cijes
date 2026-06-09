<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Evenementinscription;
use Illuminate\Http\Request;
use App\Models\Entreprisemembre;
use App\Models\Evenementressource;
use App\Services\MembreService;
use App\Services\PaiementService;
use Illuminate\Support\Facades\DB;

class EvenementController extends Controller
{
    protected MembreService $membreService;
    protected PaiementService $paiementService;

    private const OFFRETYPE_EVENEMENT = 3;

    public function __construct(MembreService $membreService, PaiementService $paiementService)
    {
        $this->membreService = $membreService;
        $this->paiementService = $paiementService;
    }

    public function index()
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $evenements = Evenement::with(['evenementtype'])
            ->where('etat', 1)
            ->where('pays_id', $membre->pays_id)
            ->whereDate('dateevenement', '>=', now())
            ->orderBy('dateevenement', 'asc')
            ->get();

        $inscriptions = Evenementinscription::where('membre_id', $membre->id)
            ->where('etat', 1)
            ->pluck('evenement_id')
            ->toArray();

        return view('evenement.index', compact('evenements', 'inscriptions'));
    }

    public function show(Evenement $evenement)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();

        $dejaInscrit = null;

        if ($membre) {
            $dejaInscrit = $evenement->inscriptions()
                ->where('membre_id', $membre->id)
                ->with('evenementinscriptiontype')
                ->first();
        }

        return view('evenement.show', compact('evenement', 'dejaInscrit'));
    }

    public function inscrireForm($id)
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $evenement = Evenement::where('etat', 1)->findOrFail($id);

        $optionsPaiement = $this->paiementService->getOptionsPaiementPourMembre($membre);

        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);
        $ressources = $this->membreService->getRessourceComptes($membre, $entrepriseIds);

        return view('evenement.inscrire', compact('evenement', 'ressources', 'optionsPaiement'));
    }

    public function inscrireStore(Request $request, $id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();
        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);

        $evenement = Evenement::where('etat', 1)->findOrFail($id);

        $contextePaiement = null;
        $contexteType = $request->input('contexte_type');
        $contexteId = $request->input('contexte_id');

        $optionsPaiement = $this->paiementService->getOptionsPaiementPourMembre($membre);

        if ($contexteType && $contexteId) {
            $contextePaiement = $this->paiementService->resolveContextePaiement($contexteType, $contexteId, $optionsPaiement);
        }

        if (!$contextePaiement) {
            $contextePaiement = $this->paiementService->autoSelectContextePaiement($optionsPaiement);
        }

        $reductions = $this->paiementService->getReductionsPourContexte($contextePaiement, self::OFFRETYPE_EVENEMENT);

        $montantOriginal = $request->input('montant') !== null
            ? (float) $request->input('montant')
            : (float) ($evenement->prix ?? 0);

        $calculReduction = $this->paiementService->calculerMontantAvecReduction($montantOriginal, $reductions);
        $montant = $calculReduction['montant_final'];

        $rules = [
            'contexte_type' => 'required|in:entreprise,accompagnement,membre',
            'contexte_id' => 'required|integer',
        ];

        if ($montant > 0) {
            $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
        }

        $request->validate($rules);

        if (Evenementinscription::where('membre_id', $membre->id)->where('evenement_id', $evenement->id)->exists()) {
            return back()->withInput()->with('error', '⚠️ Vous êtes déjà inscrit à cet événement.');
        }

        $ressourcecompte = null;
        if ($montant > 0) {
            $ressourcecompte = $this->paiementService->validateAndGetRessourceCompte(
                $request->ressourcecompte_id, $membre, $entrepriseIds
            );
        }

        DB::beginTransaction();

        try {
            $reference = $this->paiementService->generateReference('PAI-EVT');

            if ($montant > 0) {
                if (!$this->paiementService->checkRessourceCompatibility($ressourcecompte, self::OFFRETYPE_EVENEMENT)) {
                    throw new \Exception("❌ Ce type de ressource ne peut pas payer un événement.");
                }

                if ($ressourcecompte->solde < $montant) {
                    throw new \Exception("⚠️ Solde insuffisant. Montant requis: {$montant} FCFA, Solde disponible: {$ressourcecompte->solde} FCFA");
                }

                $this->paiementService->createDebitTransaction($ressourcecompte, $montant, $reference);
            }

            $contexteIds = $this->paiementService->resolveContexteIds($contextePaiement);

            Evenementressource::create([
                'montant' => $montant,
                'reference' => $reference,
                'accompagnement_id' => $contexteIds['accompagnement_id'],
                'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
                'evenement_id' => $evenement->id,
                'paiementstatut_id' => 1,
                'membre_id' => $membre->id,
                'entreprise_id' => $contexteIds['entreprise_id'],
                'spotlight' => 0,
                'etat' => 1,
            ]);

            $statutDefaut = \App\Models\Evenementinscriptiontype::where('etat', 1)->first();
            Evenementinscription::create([
                'membre_id' => $membre->id,
                'evenement_id' => $evenement->id,
                'dateevenementinscription' => now(),
                'evenementinscriptiontype_id' => $statutDefaut?->id,
                'etat' => 1,
                'spotlight' => 0,
            ]);

            DB::commit();

            if ($montant > 0) {
                $message = "✅ Inscription confirmée ! Paiement unique de {$montant} FCFA effectué.";
            } else {
                $message = "✅ Inscription gratuite confirmée ! Aucun paiement requis.";
            }

            if (isset($calculReduction['reduction']) && $calculReduction['reduction']) {
                $economie = $calculReduction['economie'];
                $message .= " Économie réalisée : {$economie} FCFA.";
            }

            return redirect()->route('evenement.show', $evenement->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
        }
    }

    public function calculerMontant(Request $request, $id)
    {
        $evenement = Evenement::findOrFail($id);
        $contexte = $request->input('contexte');
        $contexteId = $request->input('contexte_id');
        $prixBase = $request->input('prix_base');
        $duree = $request->input('duree', 1);

        try {
            $montantBase = $prixBase * $duree;

            $reductions = $this->paiementService->getReductionsPourContexte($contexte, self::OFFRETYPE_EVENEMENT);

            $calculReduction = $this->paiementService->calculerMontantAvecReduction($montantBase, $reductions);
            $montantFinal = $calculReduction['montant_final'];

            $reductionMontant = $montantBase - $montantFinal;

            return response()->json([
                'success' => true,
                'montant_base' => $montantBase,
                'montant_final' => $montantFinal,
                'reduction' => $reductionMontant,
                'reduction_pourcentage' => $reductionMontant > 0 ? round(($reductionMontant / $montantBase) * 100, 2) : 0,
                'reduction_description' => $reductionMontant > 0 ? 'Réduction appliquée' : null,
                'duree' => $duree
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du montant: ' . $e->getMessage()
            ], 500);
        }
    }
}
