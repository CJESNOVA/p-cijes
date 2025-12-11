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
use App\Models\Entreprisemembre;
use App\Models\Accompagnement;

class DiagnosticentrepriseController extends Controller
{
    public function showForm()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer les entreprises liées à ce membre via le pivot Entreprisemembre
        $entrepriseMembres = Entreprisemembre::with('entreprise')
            ->where('membre_id', $membre->id)
            ->get();

        // Extraire les entreprises (collection de modèles Entreprise)
        $entreprises = $entrepriseMembres->pluck('entreprise');

        // Chercher un diagnostic existant lié à une de ces entreprises, trié par date descendante
        $diagnostic = Diagnostic::whereIn('entreprise_id', $entreprises->pluck('id')->toArray())
            ->latest()
            ->first();

        // Charger les modules de diagnostic du type 2, actifs, triés par position
        $diagnosticmodules = Diagnosticmodule::where('diagnosticmoduletype_id', 2)
            ->where('etat', 1)
            ->orderBy('position') // tri normal sur modules
            ->with([
                'diagnosticquestions' => function ($q) {
                    $q->where('etat', 1)
                      ->orderBy('position') // tri normal sur questions
                      ->with(['diagnosticreponses' => function ($query) {
                          $query->inRandomOrder(); // tri aléatoire sur réponses uniquement
                      }]);
                }
            ])
            ->get();

        // Récupérer les réponses déjà sélectionnées groupées par question
        $existing = [];
        if ($diagnostic) {
            $existing = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->get()
                ->groupBy('diagnosticquestion_id')
                ->map(function ($group) {
                    return $group->pluck('diagnosticreponse_id')->toArray();
                });
        }

        return view('diagnosticentreprise.form', [
            'diagnosticmodules' => $diagnosticmodules,
            'existing' => $existing,
            'diagnostic' => $diagnostic,
            'entreprises' => $entreprises,
        ]);
    }

    public function store(Request $request)
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

        // 🔑 Diagnostic unique pour ce membre & entreprise
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

        $totalScore = 0;

        foreach ($answers as $question_id => $values) {
            Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                ->where('diagnosticquestion_id', $question_id)
                ->delete();

            $values = is_array($values) ? $values : [$values];

            foreach ($values as $reponse_id) {
                $reponse = Diagnosticreponse::find($reponse_id);
                $totalScore += $reponse?->score ?? 0;

                Diagnosticresultat::create([
                    'diagnostic_id'         => $diagnostic->id,
                    'diagnosticquestion_id' => $question_id,
                    'diagnosticreponse_id'  => $reponse_id,
                    'etat'                  => 1,
                ]);
            }
        }

        // Vérif questions obligatoires
        $obligatoires = Diagnosticquestion::where('etat', 1)
            ->where('obligatoire', 1)
            ->pluck('id');

        $repondues = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->whereIn('diagnosticquestion_id', $obligatoires)
            ->distinct()
            ->pluck('diagnosticquestion_id');

        if ($obligatoires->count() === $repondues->count()) {
            $diagnostic->update([
                'scoreglobal'         => $totalScore,
                'diagnosticstatut_id' => 2,
            ]);

            // Création accompagnement lié
            $accompagnement = Accompagnement::create([
                'entreprise_id'            => $request->entreprise_id,
                'accompagnementniveau_id'  => 1,
                'dateaccompagnement'       => now(),
                'accompagnementstatut_id'  => 1,
            ]);

            $diagnostic->update([
                'accompagnement_id' => $accompagnement->id,
            ]);

            return redirect()->route('diagnosticentreprise.success')
                ->with('success', "✅ Diagnostic terminé. Score : {$totalScore}");
        }

        return back()->with('success', 'Réponses enregistrées. Vous pourrez continuer plus tard.');
    }

}
