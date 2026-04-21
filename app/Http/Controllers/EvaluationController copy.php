<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function create($expertId)
    {
        return view('evaluation.create', compact('expertId'));
    }

    public function store(Request $request, $expertId)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $membre = Membre::where('user_id', Auth::id())->firstOrFail();

        Evaluation::create([
            'expert_id' => $expertId,
            'membre_id' => $membre->id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'etat' => 1,
        ]);

        return redirect()->route('expert.index')->with('success', 'Votre évaluation a bien été enregistrée.');
    }
}
