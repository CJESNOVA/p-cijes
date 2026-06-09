<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\Prestationtype;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Prestationrealisee;
use App\Models\Prestationrealiseestatut;
use App\Models\Ressourcecompte;
use App\Models\Prestationressource;
use App\Services\MembreService;
use App\Services\PaiementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrestationController extends Controller
{
    protected MembreService $membreService;
    protected PaiementService $paiementService;

    private const OFFRETYPE_PRESTATION = 1;

    public function __construct(MembreService $membreService, PaiementService $paiementService)
    {
        $this->membreService = $membreService;
        $this->paiementService = $paiementService;
    }

    public function index()
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        $prestations = Prestation::whereIn('entreprise_id', $entrepriseIds)
            ->with(['prestationtype', 'entreprise'])
            ->orderByDesc('id')
            ->get();

        return view('prestation.index', compact('prestations'));
    }


    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->get();

        $prestationtypes = Prestationtype::where('etat', 1)->get();

        return view('prestation.form', [
            'prestation' => null,
            'prestationtypes' => $prestationtypes,
            'pays_id' => $membre->pays_id,
            'entreprises' => $entreprises,
        ]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->get();

        $prestation = Prestation::findOrFail($id);
        $prestationtypes = Prestationtype::where('etat', 1)->get();

        return view('prestation.form', compact('prestation', 'prestationtypes', 'entreprises'));
    }

    public function store(Request $request)
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        $validated = $this->validateData($request);
        $validated['pays_id'] = $membre->pays_id;
        $validated['etat'] = 1;
        $validated['spotlight'] = $request->has('spotlight') ? 1 : 0;

        Prestation::create($validated);

        return redirect()->route('prestation.index')->with('success', 'Prestation créée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $prestation = Prestation::findOrFail($id);

        $validated = $this->validateData($request);

        $prestation->update($validated);

        return redirect()->route('prestation.index')->with('success', 'Prestation mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $prestation = Prestation::findOrFail($id);
        $prestation->delete();

        return redirect()->route('prestation.index')->with('success', 'Prestation supprimée avec succès.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'prix' => 'required|numeric|min:0',
            'duree' => 'required|string|max:100',
            'prestationtype_id' => 'required|exists:prestationtypes,id',
            'titre' => 'nullable|string',
            'description' => 'nullable|string',
            'entreprise_id' => 'nullable|string',
        ]);
    }

    public function liste()
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        $prestations = Prestation::with(['prestationtype', 'entreprise'])
            ->where('etat', 1)
            ->where(function($q) use ($membre, $entrepriseIds) {
                $q->where('pays_id', $membre->pays_id)
                ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        $membreId = $membre->id;

        return view('prestation.liste', compact('prestations', 'membreId'));
    }

    public function inscrireForm($id, Request $request)
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $prestation = Prestation::where('etat', 1)->findOrFail($id);

        $propositionContext = null;
        if ($request->has('proposition')) {
            $proposition = \App\Models\Proposition::find($request->input('proposition'));
            if ($proposition && $proposition->statut && $proposition->statut->titre === 'Acceptée') {
                $propositionContext = [
                    'id' => $proposition->id,
                    'prix_propose' => $proposition->prix_propose,
                ];
            }
        }

        $optionsPaiement = $this->paiementService->getOptionsPaiementPourMembre($membre);

        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);
        $ressources = $this->membreService->getRessourceComptes($membre, $entrepriseIds);

        return view('prestation.inscrire', compact('prestation', 'ressources', 'optionsPaiement', 'propositionContext'));
    }

    public function inscrireStore(Request $request, $id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();
        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);

        $prestation = Prestation::where('etat', 1)->findOrFail($id);

        $contextePaiement = null;
        $contexteType = $request->input('contexte_type');
        $contexteId = null;

        switch ($contexteType) {
            case 'entreprise':
                $contexteId = $request->input('entreprise_id');
                break;
            case 'accompagnement':
                $contexteId = $request->input('accompagnement_id');
                break;
            case 'membre':
                $contexteId = $request->input('membre_id');
                break;
        }

        $optionsPaiement = $this->paiementService->getOptionsPaiementPourMembre($membre);

        if ($contexteType && $contexteId) {
            $contextePaiement = $this->paiementService->resolveContextePaiement($contexteType, $contexteId, $optionsPaiement);
        }

        if (!$contextePaiement) {
            $contextePaiement = $this->paiementService->autoSelectContextePaiement($optionsPaiement);
        }

        $reductions = $this->paiementService->getReductionsPourContexte($contextePaiement, self::OFFRETYPE_PRESTATION);

        $montantOriginal = $prestation->prix ?? 0;
        if ($request->input('prix_propose')) {
            $montantOriginal = (float) $request->input('prix_propose');
        } elseif ($request->input('montant') !== null) {
            $montantOriginal = (float) $request->input('montant');
        }

        $calculReduction = $this->paiementService->calculerMontantAvecReduction($montantOriginal, $reductions);
        $montant = $calculReduction['montant_final'];

        $rules = [
            'contexte_type' => 'required|in:entreprise,accompagnement,membre',
        ];

        if ($montant > 0) {
            $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
        }

        $request->validate($rules);

        if ($contextePaiement['type'] === 'accompagnement_entreprise' || $contextePaiement['type'] === 'accompagnement_membre') {
            if (Prestationrealisee::where('prestation_id', $prestation->id)
                ->where('accompagnement_id', $contextePaiement['id'])
                ->exists()
            ) {
                return back()->with('error', '⚠️ Cette prestation a déjà été enregistrée pour cet accompagnement.');
            }
        }

        $ressourcecompte = null;
        if ($montant > 0) {
            $ressourcecompte = $this->paiementService->validateAndGetRessourceCompte(
                $request->ressourcecompte_id, $membre, $entrepriseIds
            );
        }

        $receveurEntrepriseId = $prestation->entreprise_id;
        $receveurCompte = Ressourcecompte::firstOrCreate(
            ['entreprise_id' => $receveurEntrepriseId, 'ressourcetype_id' => 1],
            ['membre_id' => null, 'solde' => 0, 'etat' => 1, 'spotlight' => 0]
        );

        DB::beginTransaction();

        try {
            $reference = $this->paiementService->generateReference('PAI-PREST');

            if ($montant > 0) {
                if (!$this->paiementService->checkRessourceCompatibility($ressourcecompte, self::OFFRETYPE_PRESTATION)) {
                    throw new \Exception("❌ Ce type de ressource ne peut pas payer une prestation.");
                }

                if ($ressourcecompte->solde < $montant) {
                    throw new \Exception("⚠️ Solde insuffisant. Montant requis: {$montant} FCFA, Solde disponible: {$ressourcecompte->solde} FCFA");
                }

                $this->paiementService->createDebitTransaction($ressourcecompte, $montant, $reference);
                $this->paiementService->createCreditTransaction($receveurCompte, $montant, $reference);
            }

            $contexteIds = $this->paiementService->resolveContexteIds($contextePaiement);
            $entrepriseId = $contexteIds['entreprise_id'];

            if ($montant > 0 && $entrepriseId) {
                $this->traiterPaiementPrestationViaAction($montant, $entrepriseId, $ressourcecompte, $prestation, $membre);
            }

            $prestationressource = Prestationressource::create([
                'montant' => $montant,
                'reference' => $reference,
                'accompagnement_id' => $contexteIds['accompagnement_id'],
                'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
                'prestation_id' => $prestation->id,
                'paiementstatut_id' => 1,
                'membre_id' => $membre->id,
                'entreprise_id' => $entrepriseId,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            if ($request->input('proposition_id')) {
                $proposition = \App\Models\Proposition::find($request->input('proposition_id'));
                if ($proposition) {
                    $proposition->update([
                        'prestationressource_id' => $prestationressource->id,
                        'propositionstatut_id' => 4,
                    ]);
                }
            }

            $statutDefaut = Prestationrealiseestatut::where('etat', 1)->first();
            Prestationrealisee::create([
                'prestation_id' => $prestation->id,
                'accompagnement_id' => $contexteIds['accompagnement_id'],
                'daterealisation' => now(),
                'prestationrealiseestatut_id' => $statutDefaut?->id,
                'note' => '',
                'feedback' => '',
                'spotlight' => 0,
                'etat' => 1,
            ]);

            DB::commit();

            if ($request->input('proposition_id')) {
                $message = "🎯 Paiement de la prestation '{$prestation->titre}' bien effectué suite à votre proposition acceptée !";
                if ($montant > 0) {
                    $message .= " Montant payé : " . number_format($montant, 2) . " €.";
                }
            } else {
                if ($montant > 0) {
                    $message = "✅ Inscription à la prestation '{$prestation->titre}' bien effectuée ! Paiement de " . number_format($montant, 2) . " € effectué.";
                } else {
                    $message = "✅ Inscription à la prestation '{$prestation->titre}' bien effectuée ! Aucun paiement requis.";
                }
            }

            if ($montantOriginal > $montant) {
                $economie = $montantOriginal - $montant;
                $pourcentage = round(($economie / $montantOriginal) * 100, 1);
                $message .= " 🎉 Vous avez économisé " . number_format($economie, 2) . " FCFA ({$pourcentage}% de réduction) !";
            }

            return redirect()->route('prestation.liste')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '⚠️ Erreur : ' . $e->getMessage());
        }
    }

    public function calculerMontant(Request $request, $id)
    {
        $prestation = Prestation::findOrFail($id);
        $contexte = $request->input('contexte');
        $contexteId = $request->input('contexte_id');
        $prixBase = $request->input('prix_base');
        $quantite = $request->input('quantite', 1);

        try {
            $montantBase = $prixBase * $quantite;

            $reductions = $this->paiementService->getReductionsPourContexte($contexte, self::OFFRETYPE_PRESTATION);

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
                'quantite' => $quantite
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du montant: ' . $e->getMessage()
            ], 500);
        }
    }

    private function traiterPaiementPrestationViaAction($montant, $entrepriseId, $ressourcecompte, $prestation, $membre)
    {
        $entreprise = \App\Models\Entreprise::find($entrepriseId);

        if (!$entreprise) {
            \Log::warning('Entreprise non trouvée pour paiement prestation', ['entreprise_id' => $entrepriseId]);
            return;
        }

        $actionCode = $this->determinerCodeActionPrestation($entreprise->entrepriseprofil_id, $ressourcecompte->ressourcetype_id);

        if (!$actionCode) {
            \Log::warning('Code action non déterminé', [
                'entreprise_profil_id' => $entreprise->entrepriseprofil_id,
                'ressource_type_id' => $ressourcecompte->ressourcetype_id
            ]);
            return;
        }

        try {
            $moduleController = new \App\Http\Controllers\ModuleressourceController();
            $resultat = $moduleController->attribuerModuleViaAction(
                'prestations',
                $prestation->id,
                $actionCode,
                $membre,
                [
                    'entreprise' => $entreprise,
                    'montant' => $montant,
                    'description' => "Paiement prestation {$actionCode}",
                    'reference' => 'PI-' . $prestation->id . '-' . date('YmdHis')
                ]
            );

            if ($resultat['success']) {
                \Log::info('Paiement prestation effectué avec succès', [
                    'action_code' => $actionCode,
                    'prestation_id' => $prestation->id,
                    'montant' => $montant
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors du paiement prestation via action', [
                'action_code' => $actionCode,
                'prestation_id' => $prestation->id,
                'erreur' => $e->getMessage()
            ]);
        }
    }

    private function determinerCodeActionPrestation($entrepriseProfilId, $ressourceTypeId)
    {
        $mapping = [
            1 => [ // Pépite
                3 => 'PI_PEPITE_BON',
                1 => 'PI_PEPITE_KOBO',
                4 => 'PI_PEPITE_SIKA',
            ],
            2 => [ // Émergeant
                3 => 'PI_EMERGEANT_BON',
                1 => 'PI_EMERGEANT_KOBO',
                4 => 'PI_EMERGEANT_SIKA',
            ],
            3 => [ // Élite
                3 => 'PI_ELITE_BON',
                1 => 'PI_ELITE_KOBO',
                4 => 'PI_ELITE_SIKA',
            ]
        ];

        return $mapping[$entrepriseProfilId][$ressourceTypeId] ?? null;
    }
}
