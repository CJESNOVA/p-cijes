<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Formationniveau;
use App\Models\Formationtype;
use App\Models\Participant;
use App\Models\Participantstatut;
use App\Models\Expert;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Ressourcecompte;
use App\Models\Formationressource;
use App\Models\Accompagnement;
use App\Services\MembreService;
use App\Services\PaiementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Services\RecompenseService;

class FormationController extends Controller
{
    protected MembreService $membreService;
    protected PaiementService $paiementService;

    private const OFFRETYPE_FORMATION = 2;

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

        $experts = Expert::where('membre_id', $membre->id)->pluck('id');

        $formations = Formation::whereIn('expert_id', $experts)
            ->orderByDesc('id')
            ->get();

        return view('formation.index', compact('formations'));
    }

    public function create()
    {
        $formationniveaux = Formationniveau::where('etat', 1)->get();
        $formationtypes = Formationtype::where('etat', 1)->get();

        $membre = $this->membreService->getAuthenticatedMembreOrFail();
        $experts = Expert::where('membre_id', $membre->id)->get();

        return view('formation.form', [
            'formation' => null,
            'formationniveaux' => $formationniveaux,
            'formationtypes' => $formationtypes,
            'experts' => $experts,
        ]);
    }

    public function store(Request $request)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'formationniveau_id' => 'required|exists:formationniveaus,id',
            'formationtype_id' => 'required|exists:formationtypes,id',
            'datedebut' => 'required|date',
            'datefin' => 'required|date|after_or_equal:datedebut',
            'prix' => 'nullable|numeric',
            'description' => 'nullable|string',
            'expert_id' => 'required|exists:experts,id',
        ]);

        $expert = Expert::where('membre_id', $membre->id)
            ->where('id', $validated['expert_id'])
            ->firstOrFail();

        $validated['pays_id'] = $membre->pays_id;
        $validated['etat'] = 1;
        $validated['spotlight'] = 0;

        Formation::create($validated);

        return redirect()->route('formation.index')->with('success', 'Formation créée avec succès.');
    }

    public function edit($id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();

        $expertIds = Expert::where('membre_id', $membre->id)->pluck('id');

        $formation = Formation::whereIn('expert_id', $expertIds)->findOrFail($id);

        $formationniveaux = Formationniveau::where('etat', 1)->get();
        $formationtypes = Formationtype::where('etat', 1)->get();
        $experts = Expert::where('membre_id', $membre->id)->get();

        return view('formation.form', compact('formation', 'formationniveaux', 'formationtypes', 'experts'));
    }

    public function update(Request $request, $id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();

        $expertIds = Expert::where('membre_id', $membre->id)->pluck('id');
        $formation = Formation::whereIn('expert_id', $expertIds)->findOrFail($id);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'formationniveau_id' => 'required|exists:formationniveaus,id',
            'formationtype_id' => 'required|exists:formationtypes,id',
            'datedebut' => 'required|date',
            'datefin' => 'required|date|after_or_equal:datedebut',
            'prix' => 'nullable|numeric',
            'description' => 'nullable|string',
            'expert_id' => 'required|exists:experts,id',
        ]);

        $expert = Expert::where('membre_id', $membre->id)
            ->where('id', $validated['expert_id'])
            ->firstOrFail();

        $validated['pays_id'] = $membre->pays_id;

        $formation->update($validated);

        return redirect()->route('formation.index')->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();
        $expert = Expert::where('membre_id', $membre->id)->firstOrFail();

        $formation = Formation::where('expert_id', $expert->id)->findOrFail($id);

        $formation->delete();
        return redirect()->route('formation.index')->with('success', 'Formation supprimée.');
    }

    public function show($id)
    {
        $formation = Formation::with([
            'formationniveau',
            'formationtype',
            'expert.membre',
            'participants.membre',
            'quizs' => function ($q) {
                $q->withCount('quizquestions');
            }
        ])->findOrFail($id);

        return view('formation.show', compact('formation'));
    }

    public function participants($id)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();

        $expertIds = Expert::where('membre_id', $membre->id)->pluck('id');

        $formation = Formation::findOrFail($id);

        $participants = Participant::with(['membre', 'participantstatut'])
            ->where('formation_id', $formation->id)
            ->get();

        return view('formation.participants', compact('formation', 'participants'));
    }

    public function liste()
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $expert = Expert::where('membre_id', $membre->id)->first();
        $membreId = $membre->id;

        $formations = Formation::with([
            'participants',
            'formationniveau',
            'formationtype',
            'expert.membre',
            'quizs' => function ($query) {
                $query->withCount('quizquestions');
            }
        ])
        ->where('etat', 1)
        ->where('pays_id', $membre->pays_id)
        ->orderByDesc('id')
        ->get();

        return view('formation.liste', compact('formations', 'expert', 'membreId'));
    }

    public function inscrireForm($id)
    {
        $membre = $this->membreService->getAuthenticatedMembre();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        $formation = Formation::where('etat', 1)->findOrFail($id);

        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);
        $ressources = $this->membreService->getRessourceComptes($membre, $entrepriseIds);

        $accompagnements = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entrepriseIds)
            ->get();

        return view('formation.inscrire', compact('formation', 'ressources', 'accompagnements'));
    }

    public function inscrireStore(Request $request, $id, RecompenseService $recompenseService)
    {
        $membre = $this->membreService->getAuthenticatedMembreOrFail();

        $entrepriseIds = $this->membreService->getEntrepriseIds($membre);

        $formation = Formation::where('etat', 1)->findOrFail($id);

        $montant = (float) ($formation->prix ?? 0);
        $accompagnementId = $request->input('accompagnement_id');

        $rules = [
            'accompagnement_id' => 'required|exists:accompagnements,id',
        ];

        if ($montant > 0) {
            $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
        }

        $request->validate($rules);

        if (Participant::where('membre_id', $membre->id)
            ->where('formation_id', $formation->id)
            ->exists()) {
            return back()->withInput()->with('error', '⚠️ Vous êtes déjà inscrit à cette formation.');
        }

        $ressourcecompte = null;

        if ($montant > 0) {
            $ressourcecompte = $this->paiementService->validateAndGetRessourceCompte(
                $request->ressourcecompte_id, $membre, $entrepriseIds
            );
        }

        $receveurMembreId = optional($formation->expert)->membre_id ?? null;
        $receveurEntrepriseId = $formation->entreprise_id ?? null;

        if ($receveurEntrepriseId) {
            $receveurCompte = Ressourcecompte::firstOrCreate(
                ['entreprise_id' => $receveurEntrepriseId, 'ressourcetype_id' => 1],
                ['membre_id' => $receveurMembreId, 'solde' => 0, 'etat' => 1, 'spotlight' => 0]
            );
        } elseif ($receveurMembreId) {
            $receveurCompte = Ressourcecompte::firstOrCreate(
                ['membre_id' => $receveurMembreId, 'ressourcetype_id' => 1],
                ['entreprise_id' => null, 'solde' => 0, 'etat' => 1, 'spotlight' => 0]
            );
        } else {
            return back()->withInput()->with('error', '⚠️ Impossible de déterminer le compte destinataire pour cette formation.');
        }

        DB::beginTransaction();

        try {
            $reference = $this->paiementService->generateReference('PAI-FORM');

            if ($montant > 0) {
                if (!$this->paiementService->checkRessourceCompatibility($ressourcecompte, self::OFFRETYPE_FORMATION)) {
                    throw new \Exception("❌ Ce type de ressource ne peut pas payer une formation.");
                }

                if ($ressourcecompte->solde < $montant) {
                    throw new \Exception("⚠️ Solde insuffisant dans ce compte ressource.");
                }

                $this->paiementService->createDebitTransaction($ressourcecompte, $montant, $reference);
                $this->paiementService->createCreditTransaction($receveurCompte, $montant, $reference);
            }

            Formationressource::create([
                'montant' => $montant,
                'reference' => $reference,
                'accompagnement_id' => $accompagnementId,
                'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
                'formation_id' => $formation->id,
                'paiementstatut_id' => $montant > 0 ? 1 : 2,
                'membre_id' => $membre->id,
                'entreprise_id' => $formation->entreprise_id ?? $receveurEntrepriseId ?? null,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            $statutDefaut = Participantstatut::where('etat', 1)->first();
            Participant::create([
                'membre_id' => $membre->id,
                'formation_id' => $formation->id,
                'dateparticipant' => now(),
                'participantstatut_id' => $statutDefaut?->id,
                'etat' => 1,
                'spotlight' => 0,
            ]);

            if ($montant <= 0) {
                $entreprise = Entreprise::findOrFail($formation->entreprise_id ?? $receveurEntrepriseId ?? null);
                $recompenseService->attribuerRecompense('FORMATION_GRATUITE', $membre, $entreprise ?? null, $formation->id, null);
            }

            DB::commit();

            return redirect()->route('formation.liste')
                ->with('success', $montant > 0
                    ? '✅ Inscription et paiement réussis.'
                    : '✅ Inscription gratuite réussie. 🎁 Une récompense vous a été attribuée !'
                );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inscription formation error: ' . $e->getMessage(), [
                'user_id' => $membre->id,
                'formation_id' => $formation->id,
            ]);
            return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
        }
    }
}
