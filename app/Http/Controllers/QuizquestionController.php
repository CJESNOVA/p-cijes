<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Quizquestion;
use App\Models\Quizquestiontype;
use Illuminate\Http\Request;

class QuizquestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        $questions = Quizquestion::with('quizquestiontype')
            ->where('quiz_id', $quiz->id)
            ->withCount('quizreponses')
            ->get();

        return view('quizquestion.index', compact('quiz', 'questions'));
    }

    public function create(Quiz $quiz)
    {
        $types = Quizquestiontype::where('etat', 1)->get();
        return view('quizquestion.create', compact('quiz', 'types'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'point' => 'required|integer|min:1',
            'quizquestiontype_id' => 'required|exists:quizquestiontypes,id',
        ]);

        Quizquestion::create([
            'titre' => $request->titre,
            'point' => $request->point,
            'quiz_id' => $quiz->id,
            'quizquestiontype_id' => $request->quizquestiontype_id,
            'etat' => 1,
        ]);

        return redirect()->route('quizquestion.index', $quiz)->with('success', '✅ Question ajoutée avec succès.');
    }

    public function edit(Quiz $quiz, Quizquestion $quizquestion)
    {
        $types = Quizquestiontype::where('etat', 1)->get();
        return view('quizquestion.edit', compact('quiz', 'quizquestion', 'types'));
    }

    public function update(Request $request, Quiz $quiz, Quizquestion $quizquestion)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'point' => 'required|integer|min:1',
            'quizquestiontype_id' => 'required|exists:quizquestiontypes,id',
        ]);

        $quizquestion->update($request->only('titre', 'point', 'quizquestiontype_id'));

        return redirect()->route('quizquestion.index', $quiz)->with('success', '✏️ Question mise à jour.');
    }

    public function destroy(Quiz $quiz, Quizquestion $question)
    {
        $question->delete();
        return redirect()->route('quizquestion.index', $quiz)->with('success', '🗑️ Question supprimée.');
    }
}
