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
use App\Models\Accompagnement;

use App\Services\RecompenseService;

class DiagnosticController extends Controller
{
    public function showForm()
{
        $userId = Auth::id();
        
    // Vérification du membre connecté
    $membre = Membre::where('user_id', $userId)->first();
    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Récupération du dernier diagnostic (le plus récent)
    $diagnostic = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', 0)
        ->latest()
        ->first();

    // Récupération des modules et de leurs questions/réponses actives
    $diagnosticmodules = Diagnosticmodule::where([
            ['diagnosticmoduletype_id', 1],
            ['etat', 1],
        ])
        ->orderBy('position') // tri normal sur modules
        ->with([
            'diagnosticquestions' => function ($q) {
                $q->where('etat', 1)
                    ->orderBy('position') // tri normal sur questions
                    ->with(['diagnosticreponses' => function ($query) {
                        $query->inRandomOrder(); // tri aléatoire uniquement sur réponses
                    }]);
            },
        ])
        ->get();

    // Préparation des réponses déjà enregistrées (pour pré-cocher les réponses)
    $existing = [];
    if ($diagnostic) {
        $existing = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->get()
            ->groupBy('diagnosticquestion_id')
            ->map(fn($group) => $group->pluck('diagnosticreponse_id')->toArray());
    }

    // Retour de la vue
    return view('diagnostic.form', [
        'diagnosticmodules' => $diagnosticmodules,
        'existing' => $existing,
        'diagnostic' => $diagnostic,
        'membre' => $membre,
    ]);
}




    public function store(Request $request, RecompenseService $recompenseService)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->firstOrFail();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez créer votre profil membre avant de remplir un diagnostic.');
    }

    $answers = $request->input('diagnosticreponses', []);

    // 🔍 Cherche un diagnostic EN COURS ou crée-en un nouveau si aucun n'existe
    $diagnostic = Diagnostic::where('membre_id', $membre->id)
        ->where('diagnosticstatut_id', 1) // 1 = en cours
        ->first();

    if (!$diagnostic) {
        $diagnostic = Diagnostic::create([
            'membre_id' => $membre->id,
            'diagnosticstatut_id' => 1,
            'diagnostictype_id' => 1,
            'scoreglobal' => 0,
            'etat' => 1,
        ]);
    }

    $totalScore = 0;

    foreach ($answers as $question_id => $values) {
        // 🧹 Supprimer les anciennes réponses de cette question
        Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
            ->where('diagnosticquestion_id', $question_id)
            ->delete();

        // 📝 Enregistrer les nouvelles réponses
        if (is_array($values)) {
            foreach ($values as $reponse_id) {
                $reponse = Diagnosticreponse::find($reponse_id);
                $totalScore += $reponse?->score ?? 0;

                Diagnosticresultat::create([
                    'diagnostic_id' => $diagnostic->id,
                    'diagnosticquestion_id' => $question_id,
                    'diagnosticreponse_id' => $reponse_id,
                    'etat' => 1,
                ]);
            }
        } else {
            $reponse = Diagnosticreponse::find($values);
            $totalScore += $reponse?->score ?? 0;

            Diagnosticresultat::create([
                'diagnostic_id' => $diagnostic->id,
                'diagnosticquestion_id' => $question_id,
                'diagnosticreponse_id' => $values,
                'etat' => 1,
            ]);
        }
    }

    // ✅ Vérifier si toutes les questions obligatoires sont remplies
    $obligatoires = Diagnosticquestion::where('etat', 1)
        ->where('obligatoire', 1)
        ->pluck('id');

    $repondues = Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
        ->whereIn('diagnosticquestion_id', $obligatoires)
        ->distinct('diagnosticquestion_id')
        ->pluck('diagnosticquestion_id');

    if ($obligatoires->count() === $repondues->count()) {
        // 💯 Diagnostic terminé
        $diagnostic->update([
            'scoreglobal' => $totalScore,
            'diagnosticstatut_id' => 2, // terminé
        ]);

        // 🏁 Création automatique d’un accompagnement
        $accompagnement = Accompagnement::create([
            'membre_id' => $membre->id,
            //'entreprise_id' => 0,
            'accompagnementniveau_id' => 1,
            'dateaccompagnement' => now(),
            'accompagnementstatut_id' => 1,
        ]);

        // 🔗 Lier le diagnostic à l’accompagnement
        $diagnostic->update([
            'accompagnement_id' => $accompagnement->id,
        ]);

            // 🏆 Vérifie si c’est le premier diagnostic PME du membre
            $nbDiagnostics = Diagnostic::where('membre_id', $membre->id)->where('entreprise_id', 0)
                ->where('diagnosticstatut_id', 2)
                ->count();

            if ($nbDiagnostics === 1) {
                // 🪙 Déclenche la récompense "DIAG_PME_PREMIER"
                // 💡 Utiliser le score total comme base pour le calcul en pourcentage
                $recompense = $recompenseService->attribuerRecompense('DIAG_PME_PREMIER', $membre, null, $diagnostic->id, $totalScore ?? 0);

            }

        return redirect()->route('diagnostic.success')
            ->with('success', 'Diagnostic terminé avec succès. Score : ' . $totalScore);
    }

    return redirect()->back()
        ->with('success', 'Réponses enregistrées. Vous pouvez continuer plus tard.');
}

}
