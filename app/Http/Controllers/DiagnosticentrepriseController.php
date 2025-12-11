<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Diagnostic;
use App\Models\Diagnosticmodule;
use App\Models\Diagnosticquestion;
use App\Models\Diagnosticreponse;
use App\Models\Diagnosticresultat;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Accompagnement;

use App\Services\RecompenseService;

class DiagnosticentrepriseController extends Controller
{
    public function indexForm()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
        }

        $entrepriseMembres = Entreprisemembre::with('entreprise')
            ->where('membre_id', $membre->id)
            ->get();

        $entreprises = $entrepriseMembres->pluck('entreprise');

        return view('diagnosticentreprise.choix_entreprise', compact('entreprises'));
    }
    
    public function showForm($entrepriseId)
    {
        // Récupération des modules type 2, triés par position
        $diagnosticmodules = Diagnosticmodule::where('diagnosticmoduletype_id', 2)
            ->where('etat', 1)
            ->orderBy('position')
            ->with(['diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                ->orderBy('position')
                ->with(['diagnosticreponses' => function ($query) {
                    $query->where('etat', 1)
                            ->inRandomOrder(); // mélange aléatoire des réponses
                }]);
            }])
            ->get();

        // Diagnostic existant pour cette entreprise
        $diagnostic = Diagnostic::where('entreprise_id', $entrepriseId)->first();

        // Préparer les réponses existantes (déjà cochées)
        $existing = [];
        if ($diagnostic) {
            $existing = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->get()
                ->groupBy('diagnosticquestion_id')
                ->map(fn($items) => $items->pluck('diagnosticreponse_id')->toArray())
                ->toArray(); // convertir en array pour Blade
        }

        return view('diagnosticentreprise.form', compact(
            'diagnosticmodules',
            'existing',
            'diagnostic',
            'entrepriseId'
        ));
    }


    public function store(Request $request, RecompenseService $recompenseService)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
    }

    $request->validate([
        'entreprise_id' => 'required|exists:entreprises,id',
    ]);

    $answers = $request->input('diagnosticreponses', []);
    $totalScore = 0;

    // Diagnostic unique par membre + entreprise
    $diagnostic = Diagnostic::firstOrCreate(
        [
            'entreprise_id' => $request->entreprise_id,
            'membre_id'     => $membre->id,
        ],
        [
            'diagnosticstatut_id' => 1,
            'diagnostictype_id'   => 2,
            'scoreglobal'         => 0,
            'etat'                => 1,
        ]
    );

    \DB::transaction(function () use ($answers, $diagnostic, &$totalScore) {
        foreach ($answers as $question_id => $values) {
            Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->where('diagnosticquestion_id', $question_id)
                ->delete();

            $values = is_array($values) ? $values : [$values];

            foreach ($values as $reponse_id) {
                $reponse = Diagnosticreponse::find($reponse_id);
                if ($reponse) {
                    $totalScore += $reponse->score ?? 0;

                    Diagnosticresultat::create([
                        'diagnostic_id'         => $diagnostic->id,
                        'diagnosticquestion_id' => $question_id,
                        'diagnosticreponse_id'  => $reponse_id,
                        'etat'                  => 1,
                    ]);
                }
            }
        }
    });

    // Modules d’évaluation
    $diagnosticmodules = Diagnosticmodule::where('diagnosticmoduletype_id', 2)
        ->where('etat', 1)
        ->orderBy('position')
        ->with(['diagnosticquestions' => function ($q) {
            $q->where('etat', 1)
              ->orderBy('position')
              ->with(['diagnosticreponses' => fn($query) => $query->where('etat', 1)]);
        }])
        ->get();

    // Questions obligatoires
    $obligatoires = $diagnosticmodules
        ->flatMap(fn($module) => $module->diagnosticquestions)
        ->where('obligatoire', 1)
        ->pluck('id')
        ->toArray();

    // Questions obligatoires répondues
    $repondues = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
        ->whereIn('diagnosticquestion_id', $obligatoires)
        ->distinct()
        ->pluck('diagnosticquestion_id')
        ->toArray();

    $allAnswered = empty(array_diff($obligatoires, $repondues));

    if ($allAnswered) {
        \DB::transaction(function () use ($diagnostic, $request, $membre, $totalScore) {

            // ✅ Met à jour le diagnostic
            $diagnostic->update([
                'scoreglobal'         => $totalScore,
                'diagnosticstatut_id' => 2,
            ]);

            // ✅ Vérifie / crée un accompagnement
            $accompagnement = Accompagnement::firstOrCreate(
                [
                    'entreprise_id' => $request->entreprise_id,
                    'membre_id'     => $membre->id,
                ],
                [
                    'accompagnementniveau_id' => 1,
                    'dateaccompagnement'      => now(),
                    'accompagnementstatut_id' => 1,
                ]
            );

            $diagnostic->update([
                'accompagnement_id' => $accompagnement->id,
            ]);
        });

            // 🏆 Vérifie si c’est le premier diagnostic PME du membre
            $nbDiagnostics = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', $request->entreprise_id)
                ->where('diagnosticstatut_id', 2)
                ->count();

            if ($nbDiagnostics === 1) {
                
        $entreprise = Entreprise::findOrFail($request->entreprise_id);

                // 🪙 Déclenche la récompense "DIAG_ENTREPRISE_COMPLET"
                $recompense = $recompenseService->attribuerRecompense('DIAG_ENTREPRISE_COMPLET', $membre, $entreprise ?? null, $diagnostic->id);

            }

        return redirect()->route('diagnosticentreprise.success')
            ->with('success', "✅ Diagnostic terminé. Score : {$totalScore}");
    }

    return redirect()->route('diagnosticentreprise.success')
        ->with('info', "🕓 Diagnostic partiellement rempli. Score actuel : {$totalScore}");
}



}
