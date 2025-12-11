<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Formation;
use App\Models\Membre;
use App\Models\Expert;
use App\Models\Quizmembre;
use App\Models\Quizresultat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\RecompenseService;

class QuizController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer tous les experts liés au membre
        $experts = Expert::where('membre_id', $membre->id)->pluck('id');

        // Récupérer les formations liées à ces experts
        $formations = Formation::whereIn('expert_id', $experts)->pluck('id');

        // Charger uniquement les quiz liés aux formations du membre
        $quizs = Quiz::with('formation')
            ->whereIn('formation_id', $formations)
            ->withCount('quizquestions')
            ->orderByDesc('id')
            ->get();

        return view('quiz.index', compact('quizs'));
    }

    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $experts = Expert::where('membre_id', $membre->id)->pluck('id');
        $formations = Formation::whereIn('expert_id', $experts)->where('etat', 1)->get();

        return view('quiz.form', ['quiz' => new Quiz(), 'formations' => $formations]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $request->validate([
            'titre' => 'required|string|max:255',
            'seuil_reussite' => 'required|integer|min:0|max:100',
            'formation_id' => 'required|exists:formations,id',
        ]);

        // Vérifier que la formation appartient bien au membre
        $formation = Formation::findOrFail($request->formation_id);
        if ($formation->expert->membre_id !== $membre->id) {
            return redirect()->route('quiz.index')->with('error', '❌ Cette formation ne vous appartient pas.');
        }

        Quiz::create([
            'titre' => $request->titre,
            'seuil_reussite' => $request->seuil_reussite,
            'formation_id' => $formation->id,
            'etat' => 1,
            'spotlight' => 0,
        ]);

        return redirect()->route('quiz.index')->with('success', '✅ Quiz créé avec succès.');
    }

    public function edit(Quiz $quiz)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Vérifier que le quiz appartient à l’une de ses formations
        if ($quiz->formation->expert->membre_id !== $membre->id) {
            return redirect()->route('quiz.index')->with('error', '❌ Vous ne pouvez pas modifier ce quiz.');
        }

        $experts = Expert::where('membre_id', $membre->id)->pluck('id');
        $formations = Formation::whereIn('expert_id', $experts)->where('etat', 1)->get();

        return view('quiz.form', compact('quiz', 'formations'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Vérifier que le quiz appartient au membre
        if ($quiz->formation->expert->membre_id !== $membre->id) {
            return redirect()->route('quiz.index')->with('error', '❌ Vous ne pouvez pas modifier ce quiz.');
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'seuil_reussite' => 'required|integer|min:0|max:100',
            'formation_id' => 'required|exists:formations,id',
        ]);

        $formation = Formation::findOrFail($request->formation_id);
        if ($formation->expert->membre_id !== $membre->id) {
            return redirect()->route('quiz.index')->with('error', '❌ Cette formation ne vous appartient pas.');
        }

        $quiz->update([
            'titre' => $request->titre,
            'seuil_reussite' => $request->seuil_reussite,
            'formation_id' => $formation->id,
        ]);

        return redirect()->route('quiz.index')->with('success', '✅ Quiz modifié avec succès.');
    }

    public function destroy(Quiz $quiz)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Vérifier que le quiz appartient au membre
        if ($quiz->formation->expert->membre_id !== $membre->id) {
            return redirect()->route('quiz.index')->with('error', '❌ Vous ne pouvez pas supprimer ce quiz.');
        }

        $quiz->delete();

        return redirect()->route('quiz.index')->with('success', '🗑️ Quiz supprimé avec succès.');
    }



public function listByFormation(Formation $formation)
{
    // Récupérer tous les quiz de la formation avec leurs questions et réponses
    $quizs = $formation->quizs()
        ->withCount('quizquestions')
        ->with(['quizquestions.quizreponses'])
        ->whereHas('quizquestions.quizreponses') // uniquement quiz valides
        ->get();

    return view('quiz.listByFormation', compact('formation', 'quizs'));
}

public function show(Formation $formation, Quiz $quiz)
{
    $quiz->load(['quizquestions.quizreponses']); // charge questions + réponses
    return view('quiz.show', compact('formation', 'quiz'));
}

public function submit(Request $request, Formation $formation, Quiz $quiz, RecompenseService $recompenseService)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $data = $request->all();
    $membreId = $membre->id;

    // Nettoyer les anciennes réponses pour ce quiz et ce membre
    Quizmembre::where('membre_id', $membreId)
        ->whereIn('quizquestion_id', $quiz->quizquestions->pluck('id'))
        ->delete();

    $score = 0;

    foreach ($quiz->quizquestions as $question) {
        $choix = $data['question_' . $question->id] ?? null;

        if (!$choix) {
            continue;
        }

        // Cas choix multiple
        if (is_array($choix)) {
            foreach ($choix as $reponseId) {
                Quizmembre::create([
                    'membre_id'       => $membreId,
                    'quizquestion_id' => $question->id,
                    'quizreponse_id'  => $reponseId,
                    'valeur'          => null,
                    'etat'            => 1,
                ]);
            }
        }
        // Cas texte libre (type 3)
        elseif ($question->quizquestiontype_id == 3) {
            Quizmembre::create([
                'membre_id'       => $membreId,
                'quizquestion_id' => $question->id,
                'quizreponse_id'  => null,
                'valeur'          => $choix,
                'etat'            => 1,
            ]);
        }
        // Cas choix unique
        else {
            Quizmembre::create([
                'membre_id'       => $membreId,
                'quizquestion_id' => $question->id,
                'quizreponse_id'  => $choix,
                'valeur'          => null,
                'etat'            => 1,
            ]);
        }

        // Vérifier les bonnes réponses (hors texte libre)
        if ($question->quizquestiontype_id != 3) {
            $reponses = is_array($choix) ? $choix : [$choix];
            $bonnesReponses = $question->quizreponses()
                ->where('correcte', 1)
                ->pluck('id')
                ->toArray();

            // Si la sélection correspond exactement aux bonnes réponses
            if (!array_diff($bonnesReponses, $reponses) && !array_diff($reponses, $bonnesReponses)) {
                $score++;
            }
        }
    }

    // Supprimer l’ancien résultat avant d’insérer le nouveau
    Quizresultat::where('membre_id', $membreId)
        ->where('quiz_id', $quiz->id)
        ->delete();

    // Enregistrer le résultat global
    $resultat = Quizresultat::create([
        'membre_id'             => $membreId,
        'quiz_id'               => $quiz->id,
        'score'                 => $score,
        'quizresultatstatut_id' => 1, // "terminé"
        'etat'                  => 1,
    ]);

    // ✅ Calcul du score en pourcentage
    $totalQuestions = max(1, $quiz->quizquestions->count()); // éviter la division par zéro
    $scorePourcentage = ($score / $totalQuestions) * 100;

    // ✅ Si le score atteint ou dépasse le seuil de réussite → attribuer récompense
    if ($quiz->seuil_reussite && $scorePourcentage >= $quiz->seuil_reussite) {
        $recompenseService->attribuerRecompense('QUIZ_FORMATION', $membre, null, $quiz->id);
    }

    return redirect()
        ->route('quiz', $formation->id)
        ->with('success', '✅ Vos réponses ont été enregistrées avec succès. Score : '
            . $score . '/' . $totalQuestions . ' (' . round($scorePourcentage, 2) . '%)');
}




}
