<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Membre;
use App\Models\Expert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function create($expertId)
    {
        $expert = Expert::with('membre')->findOrFail($expertId);
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();

        if ($expert->membre_id === $membre->id) {
            return redirect()->back()->with('error', '⚠️ Vous ne pouvez pas évaluer votre propre profil.');
        }
        
        $myEvaluation = null;

        if ($membre) {
            $myEvaluation = Evaluation::where('expert_id', $expert->id)
                ->where('membre_id', $membre->id)
                ->first();
        }

        return view('evaluation.create', compact('expert', 'myEvaluation'));
    }

    public function store(Request $request, $expertId)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $membre = Membre::where('user_id', Auth::id())->firstOrFail();
        $expert = Expert::findOrFail($expertId);

        if ($expert->membre_id === $membre->id) {
            return redirect()->back()->with('error', '⚠️ Vous ne pouvez pas évaluer votre propre profil.');
        }

        Evaluation::updateOrCreate(
            ['expert_id' => $expertId, 'membre_id' => $membre->id],
            ['note' => $request->note, 'commentaire' => $request->commentaire, 'etat' => 1]
        );

        return redirect()->route('expert.show', $expertId)->with('success', '✅ Votre évaluation a été enregistrée.');
    }
}
