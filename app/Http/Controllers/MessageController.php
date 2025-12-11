<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Membre;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request, $conversationId)
    {
        $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        $membre = Membre::where('user_id', Auth::id())->firstOrFail();
        $conversation = Conversation::findOrFail($conversationId);

        if (!in_array($membre->id, [$conversation->membre_id1, $conversation->membre_id2])) {
            abort(403);
        }

        Message::create([
            'contenu' => $request->contenu,
            'conversation_id' => $conversation->id,
            'membre_id' => $membre->id,
            'lu' => 0,
            'etat' => 1,
        ]);

        $conversation->touch(); // met à jour updated_at
        return redirect()->back();
    }
}
