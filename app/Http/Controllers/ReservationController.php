<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Espace;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{

    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        $reservations = Reservation::with(['espace', 'reservationstatut'])
            ->where('membre_id', $membre->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reservation.index', compact('reservations'));
    }


    public function create($espaceId)
    {
        $espace = Espace::findOrFail($espaceId);
        return view('reservation.create', compact('espace'));
    }


    public function store(Request $request, $espaceId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        $request->validate([
            'datedebut' => 'required|date',
            'datefin' => 'required|date|after_or_equal:datedebut',
            'observation' => 'nullable|string|max:1000',
        ]);

        Reservation::create([
            'membre_id' => $membre->id,
            'espace_id' => $espaceId,
            'datedebut' => $request->datedebut,
            'datefin' => $request->datefin,
            'observation' => $request->observation,
            'reservationstatut_id' => 1, // "En attente" par défaut
            'etat' => 1,
        ]);

        return redirect()->route('reservation.index')->with('success', 'Réservation envoyée avec succès.');
    }


}
