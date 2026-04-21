<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Formationniveau;
use App\Models\Formationtype;
use App\Models\Participant;
use App\Models\Participantstatut;
use App\Models\Membre;
use App\Models\Entreprisemembre;
use App\Models\Expert;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Formationressource;
use App\Models\Ressourcetypeoffretype;
use App\Models\Accompagnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Services\RecompenseService;

class FormationController extends Controller
{
    public function index()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // récupérer tous les experts liés à ce membre
    $experts = Expert::where('membre_id', $membre->id)->pluck('id');

    // récupérer les formations liées à ces experts
    $formations = Formation::whereIn('expert_id', $experts)
        ->orderByDesc('id')
        ->get();

    return view('formation.index', compact('formations'));
}


    public function create()
    {
        $formationniveaux = Formationniveau::where('etat', 1)->get();
        $formationtypes = Formationtype::where('etat', 1)->get();

        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
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
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

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

        // Vérifier que l’expert appartient bien au membre connecté
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
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Récupérer tous les experts du membre
        $expertIds = Expert::where('membre_id', $membre->id)->pluck('id');

        // Formation doit appartenir à l’un de ses experts
        $formation = Formation::whereIn('expert_id', $expertIds)->findOrFail($id);

        $formationniveaux = Formationniveau::where('etat', 1)->get();
        $formationtypes = Formationtype::where('etat', 1)->get();
        $experts = Expert::where('membre_id', $membre->id)->get();

        return view('formation.form', compact('formation', 'formationniveaux', 'formationtypes', 'experts'));
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

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

        // Vérifier que l’expert sélectionné appartient bien au membre connecté
        $expert = Expert::where('membre_id', $membre->id)
            ->where('id', $validated['expert_id'])
            ->firstOrFail();

        $validated['pays_id'] = $membre->pays_id;

        $formation->update($validated);

        return redirect()->route('formation.index')->with('success', 'Formation mise à jour avec succès.');
    }


    public function destroy($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
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
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->firstOrFail();

    // récupérer tous les experts liés à ce membre
    $expertIds = Expert::where('membre_id', $membre->id)->pluck('id');

    // formation appartenant à un des experts
    $formation = Formation::findOrFail($id);//whereIn('expert_id', $expertIds)->

    // récupérer les participants avec leurs infos
    $participants = Participant::with(['membre', 'participantstatut'])
        ->where('formation_id', $formation->id)
        ->get();

    return view('formation.participants', compact('formation', 'participants'));
}

    // Liste des formations ouvertes
    public function liste()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $expert = Expert::where('membre_id', $membre->id)->first();
    $membreId = $membre->id;

    // récupérer uniquement les formations du pays du membre connecté
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


    // Formulaire d'inscription + paiement
    public function inscrireForm($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        $formation = Formation::where('etat', 1)->findOrFail($id);

        // Récupérer les IDs des entreprises liées au membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id')
            ->toArray();

        // Comptes ressources du membre ou de ses entreprises
        $ressources = Ressourcecompte::where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id);
                if (!empty($entrepriseIds)) {
                    $q->orWhereIn('entreprise_id', $entrepriseIds);
                }
            })
            ->where('etat', 1)
            ->get();

    // Récupérer les accompagnements du membre ou de ses entreprises
    $accompagnements = Accompagnement::where('membre_id', $membre->id)
        ->orWhereIn('entreprise_id', $entrepriseIds)
        ->get();

        return view('formation.inscrire', compact('formation', 'ressources', 'accompagnements'));
    }

    // Inscription + paiement
    public function inscrireStore(Request $request, $id, RecompenseService $recompenseService)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();

    // IDs des entreprises liées au membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    $formation = Formation::where('etat', 1)->findOrFail($id);

    // 🔒 Sécurité : le montant est toujours le prix réel
    $montant = (float) ($formation->prix ?? 0);
    $accompagnementId = $request->input('accompagnement_id');

    // Validation conditionnelle
    $rules = [
        'accompagnement_id' => 'required|exists:accompagnements,id',
    ];

    // Si la formation est payante, on exige un compte de ressource
    if ($montant > 0) {
        $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
    }

    $request->validate($rules);

    // Vérifier si déjà inscrit
    if (Participant::where('membre_id', $membre->id)
        ->where('formation_id', $formation->id)
        ->exists()) {
        return back()->withInput()->with('error', '⚠️ Vous êtes déjà inscrit à cette formation.');
    }

    $ressourcecompte = null;

    // 💰 Gestion des formations payantes
    if ($montant > 0) {
        $ressourcecompte = Ressourcecompte::where('id', $request->ressourcecompte_id)
            ->where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id)
                    ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->firstOrFail();
    }

    // Déterminer le receveur
    $receveurMembreId = optional($formation->expert)->membre_id ?? null;
    $receveurEntrepriseId = $formation->entreprise_id ?? null;

    // Trouver ou créer le compte destinataire
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
        $reference = 'PAI-FORM-' . strtoupper(Str::random(8));

        // 💵 S’il y a paiement, on traite la transaction
        if ($montant > 0) {
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 2)
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer une formation.");
            }

            if ($ressourcecompte->solde < $montant) {
                throw new \Exception("⚠️ Solde insuffisant dans ce compte ressource.");
            }

            // Débit du payeur
            Ressourcetransaction::create([
                'montant' => -$montant,
                'reference' => $reference,
                'ressourcecompte_id' => $ressourcecompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 2,
                'entreprise_id' => $ressourcecompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $ressourcecompte->decrement('solde', $montant);

            // Crédit du receveur
            Ressourcetransaction::create([
                'montant' => $montant,
                'reference' => $reference,
                'ressourcecompte_id' => $receveurCompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 1,
                'entreprise_id' => $receveurCompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $receveurCompte->increment('solde', $montant);
        }

        // 📘 Trace du paiement (ou inscription gratuite)
        Formationressource::create([
            'montant' => $montant,
            'reference' => $reference,
            'accompagnement_id' => $accompagnementId,
            'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
            'formation_id' => $formation->id,
            'paiementstatut_id' => $montant > 0 ? 1 : 2, // 1 = payé, 2 = gratuit
            'membre_id' => $membre->id,
            'entreprise_id' => $formation->entreprise_id ?? $receveurEntrepriseId ?? null,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        // 🧍 Inscription du participant
        $statutDefaut = Participantstatut::where('etat', 1)->first();
        Participant::create([
            'membre_id' => $membre->id,
            'formation_id' => $formation->id,
            'dateparticipant' => now(),
            'participantstatut_id' => $statutDefaut?->id,
            'etat' => 1,
            'spotlight' => 0,
        ]);

        // 🎁 Récompense automatique si formation gratuite
        if ($montant <= 0) {
                
        $entreprise = Entreprise::findOrFail($formation->entreprise_id ?? $receveurEntrepriseId ?? null);

            // 💡 Pas de montant logique pour une formation gratuite, utilisation de points fixes
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
