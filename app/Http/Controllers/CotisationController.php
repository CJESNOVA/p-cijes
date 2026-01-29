<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\Cotisationtype;
use App\Models\Cotisationressource;
use App\Models\Entreprise;
use App\Models\Membre;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Operationtype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CotisationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Récupérer uniquement les entreprises qui sont membres CJES et associées à l'utilisateur
        $entreprises = Entreprise::where('est_membre_cijes', true)
            ->whereHas('entreprisesmembres', function ($query) use ($userId) {
                $query->whereHas('membre', function ($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });
            })->with(['cotisations' => function ($query) {
                $query->with('cotisationtype')
                      ->orderBy('created_at', 'desc');
            }])->get();

        $cotisationtypes = Cotisationtype::where('etat', true)->get();

        return view('cotisation.index', compact('entreprises', 'cotisationtypes'));
    }

    public function create($entrepriseId)
    {
        $entreprise = Entreprise::findOrFail($entrepriseId);
        
        // Vérifier que l'utilisateur a le droit d'ajouter une cotisation pour cette entreprise
        $userId = Auth::id();
        $hasAccess = $entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour ajouter une cotisation à cette entreprise.');
        }

        // Récupérer les types de cotisations disponibles pour le profil de l'entreprise
        $entreprise->load('entrepriseprofil');
        
        // Types de cotisations génériques (entrepriseprofil_id = 0) + spécifiques au profil
        $cotisationtypes = Cotisationtype::where('etat', true)
            ->where(function($query) use ($entreprise) {
                $query->where('entrepriseprofil_id', 0) // Types génériques
                      ->orWhere('entrepriseprofil_id', $entreprise->entrepriseprofil_id); // Types spécifiques au profil
            })
            ->with('entrepriseprofil')
            ->get();

        // Récupérer les comptes ressources disponibles pour l'entreprise et ses membres
        $membreIds = $entreprise->entreprisesmembres()->pluck('membre_id')->toArray();
        
        $ressourcecomptes = Ressourcecompte::where(function($query) use ($entrepriseId, $membreIds) {
                $query->where('entreprise_id', $entrepriseId)
                      ->orWhereIn('membre_id', $membreIds);
            })
            ->where('etat', 1)
            ->where('solde', '>', 0) // Uniquement les comptes avec solde positif
            ->orderBy('solde', 'desc')
            ->get();

        return view('cotisation.create', compact('entreprise', 'cotisationtypes', 'ressourcecomptes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
            'cotisationtype_id' => 'required|exists:cotisationtypes,id',
            'ressourcecompte_id' => 'required|exists:ressourcecomptes,id',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        // Vérifier que l'utilisateur a le droit d'ajouter une cotisation pour cette entreprise
        $userId = Auth::id();
        $entreprise = Entreprise::findOrFail($request->entreprise_id);
        $hasAccess = $entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour ajouter une cotisation à cette entreprise.');
        }

        // Récupérer le type de cotisation
        $cotisationtype = Cotisationtype::findOrFail($request->cotisationtype_id);
        
        // Récupérer le compte ressource sélectionné
        $ressourcecompte = Ressourcecompte::findOrFail($request->ressourcecompte_id);
        
        // Vérifier que le compte ressource appartient bien à l'entreprise ou à un membre de l'entreprise
        $membreIds = $entreprise->entreprisesmembres()->pluck('membre_id')->toArray();
        $isValidAccount = ($ressourcecompte->entreprise_id == $entreprise->id) || 
                         in_array($ressourcecompte->membre_id, $membreIds);
        
        if (!$isValidAccount) {
            return redirect()->back()->with('error', 'Le compte ressource sélectionné n\'est pas valide pour cette entreprise.');
        }
        
        // Vérifier le solde
        if ($ressourcecompte->solde < $cotisationtype->montant) {
            return redirect()->back()->with('error', 'Solde insuffisant. Montant requis : ' . number_format($cotisationtype->montant, 2) . ' FCFA. Solde disponible : ' . number_format($ressourcecompte->solde, 2) . ' FCFA.');
        }
        
        // Calculer les dates automatiquement en utilisant nombre_jours
        $dateDuJour = now();
        $dateDebut = $dateDuJour->copy();
        $dateFin = $dateDuJour->copy();
        $dateEcheance = $dateDuJour->copy();
        
        // Utiliser nombre_jours pour calculer la fin et l'échéance
        $jours = $cotisationtype->nombre_jours > 0 ? $cotisationtype->nombre_jours : 30; // 30 jours par défaut
        $dateFin->addDays($jours);
        $dateEcheance->addDays($jours);

        $reference = 'COT-' . strtoupper(Str::random(8));

        // Créer la transaction de débit dans le compte ressource sélectionné
        $transaction = Ressourcetransaction::create([
            'montant' => -$cotisationtype->montant,
            'reference' => $reference,
            'ressourcecompte_id' => $ressourcecompte->id,
            'datetransaction' => now(),
            'operationtype_id' => 2, // Débit
            'spotlight' => true,
            'etat' => 1,
        ]);

        // Mettre à jour le solde du compte ressource
        $ressourcecompte->decrement('solde', $cotisationtype->montant);

        // Créer la cotisation
        $cotisation = Cotisation::create([
            'entreprise_id' => $request->entreprise_id,
            'cotisationtype_id' => $request->cotisationtype_id,
            'montant' => $cotisationtype->montant,
            'montant_paye' => $cotisationtype->montant, // Payée immédiatement
            'montant_restant' => 0,
            'devise' => 'XOF', // Par défaut
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'date_echeance' => $dateEcheance,
            'date_paiement' => now(),
            'statut' => 'paye',
            'est_a_jour' => true,
            'nombre_rappels' => 0,
            'mode_paiement' => 'Ressource KOBO',
            'commentaires' => $request->commentaires,
            'etat' => true,
        ]);

        // Créer l'enregistrement cotisationressource
        Cotisationressource::create([
            'montant' => $cotisationtype->montant,
            'reference' => $reference,
            'ressourcecompte_id' => $ressourcecompte->id,
            'cotisation_id' => $cotisation->id,
            'membre_id' => $ressourcecompte->membre_id,
            'entreprise_id' => $entreprise->id,
            'paiementstatut_id' => 1, // Payé
            'spotlight' => true,
            'etat' => true,
        ]);

        return redirect()->route('cotisation.index')
                    ->with('success', 'Cotisation ' . $cotisationtype->titre . ' payée avec succès ! ' . number_format($cotisationtype->montant, 2) . ' XOF débités du compte ressource.');
    }

    public function edit($id)
    {
        $cotisation = Cotisation::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier cette cotisation
        $userId = Auth::id();
        $hasAccess = $cotisation->entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour modifier cette cotisation.');
        }

        $cotisationtypes = Cotisationtype::where('etat', true)->get();

        return view('cotisation.edit', compact('cotisation', 'cotisationtypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cotisationtype_id' => 'required|exists:cotisationtypes,id',
            'montant' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'date_echeance' => 'required|date|after:date_debut',
            'mode_paiement' => 'nullable|string|max:50',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        $cotisation = Cotisation::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier cette cotisation
        $userId = Auth::id();
        $hasAccess = $cotisation->entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour modifier cette cotisation.');
        }

        $cotisation->update([
            'cotisationtype_id' => $request->cotisationtype_id,
            'montant' => $request->montant,
            'montant_restant' => $request->montant - $cotisation->montant_paye,
            'devise' => $request->devise,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'date_echeance' => $request->date_echeance,
            'mode_paiement' => $request->mode_paiement,
            'commentaires' => $request->commentaires,
        ]);

        return redirect()->route('cotisation.index')
                    ->with('success', 'Cotisation mise à jour avec succès !');
    }

    public function destroy($id)
    {
        $cotisation = Cotisation::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de supprimer cette cotisation
        $userId = Auth::id();
        $hasAccess = $cotisation->entreprise->membres()->where('user_id', $userId)
                                                      ->where('est_membre_cijes', true)
                                                      ->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour supprimer cette cotisation.');
        }

        $cotisation->delete();

        return redirect()->route('cotisation.index')
                    ->with('success', 'Cotisation supprimée avec succès !');
    }

    public function markAsPaid($id)
    {
        $cotisation = Cotisation::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier cette cotisation
        $userId = Auth::id();
        $hasAccess = $cotisation->entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour modifier cette cotisation.');
        }

        // Si déjà payée, ne rien faire
        if ($cotisation->statut === 'paye') {
            return redirect()->back()->with('info', 'Cette cotisation est déjà payée.');
        }

        // Vérifier si l'utilisateur a une ressource de type 1 (KOBO) suffisante
        $membre = Membre::where('user_id', $userId)->first();
        $ressourceKOBO = Ressourcecompte::where('membre_id', $membre->id)
                                    ->where('ressourcetype_id', 1) // KOBO
                                    ->where('etat', true)
                                    ->first();

        if (!$ressourceKOBO) {
            return redirect()->back()->with('error', 'Vous n\'avez pas de compte KOBO pour effectuer cette opération.');
        }

        $montantAPayer = $cotisation->montant_restant;
        if ($ressourceKOBO->solde < $montantAPayer) {
            return redirect()->back()->with('error', 'Solde insuffisant. Montant à payer : ' . number_format($montantAPayer, 2) . ' FCFA. Votre solde actuel : ' . number_format($ressourceKOBO->solde, 2) . ' FCFA.');
        }

        // Mettre à jour la cotisation
        $cotisation->update([
            'montant_paye' => $cotisation->montant,
            'montant_restant' => 0,
            'statut' => 'paye',
            'est_a_jour' => true,
            'date_paiement' => now(),
            'mode_paiement' => 'Ressource KOBO',
        ]);

        $reference = 'COT-' . strtoupper(Str::random(8));

        // Créer la transaction de débit dans la ressource KOBO
        $transaction = Ressourcetransaction::create([
            'montant' => $montantAPayer,
            'reference' => $reference,
            'ressourcecompte_id' => $ressourceKOBO->id,
            'datetransaction' => now(),
            'operationtype_id' => 2, // Débit
            'spotlight' => true,
            'etat' => 1,
        ]);

        // Créer l'enregistrement cotisationressource
        Cotisationressource::create([
            'montant' => $montantAPayer,
            'reference' => $reference,
            'ressourcecompte_id' => $ressourceKOBO->id,
            'cotisation_id' => $cotisation->id,
            'membre_id' => $membre->id,
            'entreprise_id' => $cotisation->entreprise->id,
            'paiementstatut_id' => 1, // Payé
            'spotlight' => true,
            'etat' => true,
        ]);

        // Mettre à jour le solde du compte KOBO
        $ressourceKOBO->decrement('solde', $montantAPayer);

        return redirect()->route('cotisation.index')
                    ->with('success', 'Cotisation payée avec succès ! ' . number_format($montantAPayer, 2) . ' KOBO débités de votre compte.');
    }
}
