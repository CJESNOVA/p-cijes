<?php

namespace App\Http\Controllers;

use App\Models\Quizquestion;
use App\Models\Quizreponse;
use Illuminate\Http\Request;

class QuizreponseController extends Controller
{
    // Liste des réponses d’une question
    public function index(Quizquestion $quizquestion)
    {
        $reponses = Quizreponse::where('quizquestion_id', $quizquestion->id)->get();
        return view('quizreponse.index', compact('quizquestion', 'reponses'));
    }

    // Formulaire création
    public function create(Quizquestion $quizquestion)
    {
        return view('quizreponse.create', compact('quizquestion'));
    }

    // Sauvegarde création
    public function store(Request $request, Quizquestion $quizquestion)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'correcte' => 'required|boolean',
        ]);

        Quizreponse::create([
            'text' => $request->text,
            'correcte' => $request->correcte,
            'quizquestion_id' => $quizquestion->id,
            'etat' => 1,
        ]);

        return redirect()
            ->route('quizreponse.index', ['quiz' => $quizquestion->quiz_id, 'quizquestion' => $quizquestion->id])
            ->with('success', '✅ Réponse ajoutée avec succès.');
    }

    // Formulaire édition
    public function edit(Quizquestion $quizquestion, Quizreponse $quizreponse)
    {
        return view('quizreponse.edit', compact('quizquestion', 'quizreponse'));
    }

    // Sauvegarde édition
    public function update(Request $request, Quizquestion $quizquestion, Quizreponse $quizreponse)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'correcte' => 'required|boolean',
        ]);

        $quizreponse->update($request->only('text', 'correcte'));

        return redirect()
            ->route('quizreponse.index', ['quiz' => $quizquestion->quiz_id, 'quizquestion' => $quizquestion->id])
            ->with('success', '✏️ Réponse mise à jour.');
    }

    // Suppression
    public function destroy(Quizquestion $quizquestion, Quizreponse $quizreponse)
    {
        $quizreponse->delete();

        return redirect()
            ->route('quizreponse.index', ['quiz' => $quizquestion->quiz_id, 'quizquestion' => $quizquestion->id])
            ->with('success', '🗑️ Réponse supprimée.');
    }
}
