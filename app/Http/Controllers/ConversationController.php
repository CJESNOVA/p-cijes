<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Membre;
use App\Models\Parrainage;
use App\Models\Expert;
use App\Models\Entreprisemembre;
use App\Models\Accompagnement;
use App\Models\Accompagnementconseiller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    // Liste des conversations du membre connecté
    public function index()
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();

    $conversations = Conversation::where('membre_id1', $membre->id)
        ->orWhere('membre_id2', $membre->id)
        ->with(['membre1', 'membre2', 'messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])
        ->orderByDesc('updated_at')
        ->get();

    return view('message.index', compact('conversations', 'membre'));
}


    // Ouvrir une conversation spécifique
    public function show($conversationId)
{
    $conversation = Conversation::with('messages.membre')->findOrFail($conversationId);
    
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();

    // Marquer les messages reçus comme lus
    $conversation->messages()
        ->where('membre_id', '!=', $membre->id)
        ->where('lu', 0)
        ->update(['lu' => 1]);

    return view('message.show', compact('conversation', 'membre'));
}

    // Créer une conversation entre deux membres
    public function start($membreId)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();

        // Vérifie si une conversation existe déjà
        $conversation = Conversation::where(function ($q) use ($membre, $membreId) {
                $q->where('membre_id1', $membre->id)->where('membre_id2', $membreId);
            })
            ->orWhere(function ($q) use ($membre, $membreId) {
                $q->where('membre_id1', $membreId)->where('membre_id2', $membre->id);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'membre_id1' => $membre->id,
                'membre_id2' => $membreId,
                'etat' => 1,
            ]);
        }

        return redirect()->route('message.show', $conversation->id);
    }

    public function create()
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();

    $membresCollection = collect();

    // Parrainages où je suis le parrain
    $membresParrainages = Parrainage::with('membrefilleul')
        ->where('membre_parrain_id', $membre->id)
        ->get()
        ->pluck('membrefilleul')
        ->filter()
        ->map(function($m) {
            $m->roles = ['filleul'];
            return $m;
        });
    $membresCollection = $membresCollection->merge($membresParrainages);

    // Parrainages où je suis le filleul
    $membresFilleuls = Parrainage::with('membreparrain')
        ->where('membre_filleul_id', $membre->id)
        ->get()
        ->pluck('membreparrain')
        ->filter()
        ->map(function($m) {
            $m->roles = ['parrain'];
            return $m;
        });
    $membresCollection = $membresCollection->merge($membresFilleuls);

    // Experts du même pays
    $membresExperts = Expert::with('membre')
        ->whereHas('membre', function ($q) use ($membre) {
            $q->where('pays_id', $membre->pays_id);
        })
        ->where('etat', 1)
        ->get()
        ->pluck('membre')
        ->filter()
        ->map(function($m) {
            $m->roles = ['expert'];
            return $m;
        });
    $membresCollection = $membresCollection->merge($membresExperts);

    // Entreprises du membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id');

    // Accompagnements où le membre est bénéficiaire (direct ou via entreprise)
    $accompagnementIds = Accompagnement::where('membre_id', $membre->id)
        ->orWhereIn('entreprise_id', $entrepriseIds)
        ->pluck('id')
        ->toArray();

    // Accompagnements liés au membre en tant que conseiller
    $accompagnementConseillerIds = Accompagnementconseiller::whereHas('conseiller', function ($q) use ($membre) {
            $q->where('membre_id', $membre->id);
        })
        ->pluck('accompagnement_id')
        ->toArray();

    $allAccompagnementIds = array_unique(array_merge($accompagnementIds, $accompagnementConseillerIds));

    // Conseillers liés aux accompagnements du membre
    $membresConseillers = Accompagnementconseiller::with('conseiller.membre')
        ->whereIn('accompagnement_id', $allAccompagnementIds)
        ->get()
        ->pluck('conseiller.membre')
        ->filter()
        ->map(function($m) {
            $m->roles = ['conseiller'];
            return $m;
        });
    $membresCollection = $membresCollection->merge($membresConseillers);

    // Filtrer, exclure soi-même, trier, unique
    $membres = $membresCollection
        ->filter(function($m) use ($membre) {
            return $m && $m->id !== $membre->id && $m->etat == 1;
        })
        ->map(function($m) {
            // Si un membre a plusieurs rôles, fusionner les rôles existants
            if(!isset($m->roles)) $m->roles = [];
            return $m;
        })
        ->groupBy('id') // regroupement par id pour fusionner roles
        ->map(function($group) {
            $member = $group->first();
            $member->roles = $group->pluck('roles')->flatten()->unique()->values()->toArray();
            return $member;
        })
        ->values()
        ->sortBy('prenom');

    return view('conversation.create', compact('membres', 'membre'));
}


    // Crée une nouvelle conversation ou redirige vers l’existante
    public function store(Request $request)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();
        $autreId = $request->input('membre_id');

        // Vérifie si la conversation existe déjà entre les deux membres
        $conversation = Conversation::where(function ($q) use ($membre, $autreId) {
                $q->where('membre_id1', $membre->id)->where('membre_id2', $autreId);
            })
            ->orWhere(function ($q) use ($membre, $autreId) {
                $q->where('membre_id1', $autreId)->where('membre_id2', $membre->id);
            })
            ->first();

        // Si aucune conversation n’existe, on la crée
        if (!$conversation) {
            $conversation = Conversation::create([
                'membre_id1' => $membre->id,
                'membre_id2' => $autreId,
                'etat' => 1,
                'spotlight' => 0,
            ]);
        }

        // Redirige vers la vue des messages
        return redirect()->route('message.show', $conversation->id);
    }


}
