<?php

namespace App\Http\Controllers;

use App\Models\Espace;
use App\Models\Reservation;
use App\Models\Reservationstatut;
use Illuminate\Http\Request;
use App\Models\Accompagnement;
use App\Models\Espaceressource;
use App\Services\MembreService;
use App\Services\PaiementService;
use Illuminate\Support\Facades\DB;

class EspaceController extends Controller
{
    protected MembreService $membreService;
    protected PaiementService $paiementService;

    private const OFFRETYPE_ESPACE = 4;

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

        $espaces = Espace::with('reservationsAVenir')
            ->with('espacetype')
            ->where('etat', 1)
            ->where('pays_id', $membre->pays_id)
            ->get();

        return view('espace.index', compact('espaces'));
    }

    public function show($id)
    {
        $espace = Espace::with('reservationsAVenir')->with('espacetype')->findOrFail($id);
        return view('espace.show', compact('espace'));
    }

    public function reserverForm($id)
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $espace = Espace::where('etat', 1)->findOrFail($id);

        $optionsPaiement = $this->paiementService->getOptionsPaiementPourMembre($membre);

        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);
        $ressources = $this->membreService->getRessourceComptes($membre, $entrepriseIds);

        $accompagnements = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entrepriseIds)
            ->get();

        if ($accompagnements->isEmpty() && empty($entrepriseIds)) {
            return redirect()->route('espace.index')
                ->with('error', '⚠️ Vous devez avoir au moins une entreprise ou un accompagnement pour effectuer une réservation.');
        }

        $doitChoisirPaiement = false;
        $contexteAuto = null;

        if (count($optionsPaiement['accompagnements'] ?? []) === 1) {
            $contexteAuto = $optionsPaiement['accompagnements'][0];
        } elseif (count($optionsPaiement['entreprises'] ?? []) === 1 && empty($optionsPaiement['accompagnements'])) {
            $contexteAuto = $optionsPaiement['entreprises'][0];
        } elseif (empty($optionsPaiement['entreprises']) && empty($optionsPaiement['accompagnements'])) {
            $contexteAuto = $optionsPaiement['membre'];
        } else {
            $doitChoisirPaiement = true;
        }

        $reductionsApplicables = $contexteAuto
            ? $this->paiementService->getReductionsPourContexte($contexteAuto, self::OFFRETYPE_ESPACE)
            : collect();

        return view('espace.reserver', compact(
            'espace',
            'ressources',
            'accompagnements',
            'optionsPaiement',
            'doitChoisirPaiement',
            'contexteAuto',
            'reductionsApplicables'
        ));
    }

    public function reserverStore(Request $request, $id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();
        $espace = Espace::where('etat', 1)->findOrFail($id);

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

        $reductions = $this->paiementService->getReductionsPourContexte($contextePaiement, self::OFFRETYPE_ESPACE);

        $montantOriginal = $request->input('montant') !== null
            ? (float) $request->input('montant')
            : (float) ($espace->prix ?? 0);

        $calculReduction = $this->paiementService->calculerMontantAvecReduction($montantOriginal, $reductions);
        $montant = $calculReduction['montant_final'];

        $rules = [
            'datedebut' => 'required|date|after_or_equal:today',
            'datefin' => 'required|date|after:datedebut',
            'contexte_type' => 'required|in:entreprise,accompagnement,membre',
            'contexte_id' => 'required|integer',
        ];

        if ($montant > 0) {
            $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
        }

        $request->validate($rules);

        $conflict = Reservation::where('espace_id', $espace->id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('datedebut', [$request->datedebut, $request->datefin])
                  ->orWhereBetween('datefin', [$request->datedebut, $request->datefin]);
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', '⚠️ Cet espace est déjà réservé sur cette période.');
        }

        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);

        $ressourcecompte = null;
        if ($montant > 0) {
            $ressourcecompte = $this->paiementService->validateAndGetRessourceCompte(
                $request->ressourcecompte_id, $membre, $entrepriseIds
            );
        }

        DB::beginTransaction();

        try {
            $reference = $this->paiementService->generateReference('PAI-ESP');

            if ($montant > 0) {
                if (!$this->paiementService->checkRessourceCompatibility($ressourcecompte, self::OFFRETYPE_ESPACE)) {
                    throw new \Exception("❌ Ce type de ressource ne peut pas payer un espace.");
                }

                if ($ressourcecompte->solde < $montant) {
                    throw new \Exception("⚠️ Solde insuffisant. Montant requis: {$montant} FCFA, Solde disponible: {$ressourcecompte->solde} FCFA");
                }

                $this->paiementService->createDebitTransaction($ressourcecompte, $montant, $reference);
            }

            $contexteIds = $this->paiementService->resolveContexteIds($contextePaiement);

            Espaceressource::create([
                'montant' => $montant,
                'reference' => $reference,
                'accompagnement_id' => $contexteIds['accompagnement_id'],
                'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
                'espace_id' => $espace->id,
                'paiementstatut_id' => 1,
                'membre_id' => $membre->id,
                'entreprise_id' => $contexteIds['entreprise_id'],
                'spotlight' => 0,
                'etat' => 1,
            ]);

            $statutDefaut = Reservationstatut::where('etat', 1)->first();
            Reservation::create([
                'membre_id' => $membre->id,
                'espace_id' => $espace->id,
                'datedebut' => $request->datedebut,
                'datefin' => $request->datefin,
                'observation' => $request->observation,
                'reservationstatut_id' => $statutDefaut->id ?? 1,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            DB::commit();

            if ($montant > 0) {
                $message = "✅ Réservation confirmée ! Paiement unique de {$montant} FCFA effectué.";
            } else {
                $message = "✅ Réservation gratuite confirmée ! Aucun paiement requis.";
            }

            if (isset($calculReduction['reduction']) && $calculReduction['reduction']) {
                $economie = $calculReduction['economie'];
                $message .= " Économie réalisée : {$economie} FCFA.";
            }

            return redirect()->route('espace.show', $espace->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
        }
    }

    public function calculerMontant(Request $request, $id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();
        $espace = Espace::where('etat', 1)->findOrFail($id);

        $contexteType = $request->input('contexte_type');
        $contexteId = $request->input('contexte_id');
        $montantOriginal = (float) $request->input('montant', $espace->prix ?? 0);

        $contextePaiement = null;
        if ($contexteType && $contexteId) {
            $optionsPaiement = $this->paiementService->getOptionsPaiementPourMembre($membre);
            $contextePaiement = $this->paiementService->resolveContextePaiement($contexteType, $contexteId, $optionsPaiement);
        }

        if (!$contextePaiement) {
            return response()->json([
                'error' => 'Contexte de paiement non valide'
            ], 400);
        }

        $reductions = $this->paiementService->getReductionsPourContexte($contextePaiement, self::OFFRETYPE_ESPACE);
        $calculReduction = $this->paiementService->calculerMontantAvecReduction($montantOriginal, $reductions);

        return response()->json([
            'montant_original' => $montantOriginal,
            'montant_final' => $calculReduction['montant_final'],
            'economie' => $calculReduction['economie'],
            'reduction' => $calculReduction['reduction'] ? [
                'titre' => $calculReduction['reduction']->titre,
                'pourcentage' => $calculReduction['reduction']->pourcentage,
                'montant' => $calculReduction['reduction']->montant,
            ] : null,
            'contexte' => [
                'nom' => $contextePaiement['nom'],
                'type' => $contextePaiement['type'],
                'est_cjes' => $contextePaiement['est_cjes'],
                'cotisation_a_jour' => $contextePaiement['cotisation_a_jour'],
                'profil_id' => $contextePaiement['profil_id'],
            ]
        ]);
    }
}
