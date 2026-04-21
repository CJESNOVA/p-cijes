<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Abonnementtype;
use App\Models\Abonnementressource;
use App\Models\Entreprise;
use App\Models\Membre;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use App\Models\Operationtype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\RecompenseService;

class AbonnementController extends Controller
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
            })->with(['abonnements' => function ($query) {
                $query->with('abonnementtype')
                      ->orderBy('created_at', 'desc');
            }])->get();

        $abonnementtypes = Abonnementtype::where('etat', true)->get();

        return view('abonnement.index', compact('entreprises', 'abonnementtypes'));
    }

    public function create($entrepriseId)
    {
        $entreprise = Entreprise::findOrFail($entrepriseId);
        
        // Vérifier que l'utilisateur a le droit d'ajouter un abonnement pour cette entreprise
        $userId = Auth::id();
        $hasAccess = $entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour ajouter un abonnement à cette entreprise.');
        }

        // Récupérer les types d'abonnements disponibles pour le profil de l'entreprise
        $entreprise->load('entrepriseprofil');
        
        // Types d'abonnements génériques (entrepriseprofil_id = 0) + spécifiques au profil
        $abonnementtypes = Abonnementtype::where('etat', true)
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

        return view('abonnement.create', compact('entreprise', 'abonnementtypes', 'ressourcecomptes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
            'abonnementtype_id' => 'required|exists:abonnementtypes,id',
            'ressourcecompte_id' => 'required|exists:ressourcecomptes,id',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        // Vérifier que l'utilisateur a le droit d'ajouter un abonnement pour cette entreprise
        $userId = Auth::id();
        $entreprise = Entreprise::findOrFail($request->entreprise_id);
        $hasAccess = $entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour ajouter un abonnement à cette entreprise.');
        }

        // Récupérer le type d'abonnement
        $abonnementtype = Abonnementtype::findOrFail($request->abonnementtype_id);
        
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
        if ($ressourcecompte->solde < $abonnementtype->montant) {
            return redirect()->back()->with('error', 'Solde insuffisant. Montant requis : ' . number_format($abonnementtype->montant, 2) . ' FCFA. Solde disponible : ' . number_format($ressourcecompte->solde, 2) . ' FCFA.');
        }
        
        // Vérifier compatibilité ressource ↔ offre (abonnement = offretype_id 6)
        $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
            ->where('offretype_id', 6) // 6 = abonnements
            ->exists();

        if (!$isCompatible) {
            return redirect()->back()->with('error', "❌ Ce type de ressource ne peut pas payer un abonnement.");
        }
        
        // Calculer les dates automatiquement en utilisant nombre_jours
        $dateDuJour = now();
        $dateDebut = $dateDuJour->copy();
        $dateFin = $dateDuJour->copy();
        $dateEcheance = $dateDuJour->copy();
        
        // Utiliser nombre_jours pour calculer la fin et l'échéance
        $jours = $abonnementtype->nombre_jours > 0 ? $abonnementtype->nombre_jours : 30; // 30 jours par défaut
        $dateFin->addDays($jours);
        $dateEcheance->addDays($jours);

        $reference = 'ABO-' . strtoupper(Str::random(8));

        // Créer la transaction de débit dans le compte ressource sélectionné
        $transaction = Ressourcetransaction::create([
            'montant' => -$abonnementtype->montant,
            'reference' => $reference,
            'ressourcecompte_id' => $ressourcecompte->id,
            'datetransaction' => now(),
            'operationtype_id' => 2, // Débit
            'spotlight' => true,
            'etat' => 1,
        ]);

        // Mettre à jour le solde du compte ressource
        $ressourcecompte->decrement('solde', $abonnementtype->montant);

        // Créer l'abonnement
        $abonnement = Abonnement::create([
            'entreprise_id' => $request->entreprise_id,
            'abonnementtype_id' => $request->abonnementtype_id,
            'montant' => $abonnementtype->montant,
            'montant_paye' => $abonnementtype->montant, // Payé immédiatement
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

        // Créer l'enregistrement abonnementressource
        Abonnementressource::create([
            'montant' => $abonnementtype->montant,
            'reference' => $reference,
            'ressourcecompte_id' => $ressourcecompte->id,
            'abonnement_id' => $abonnement->id,
            'membre_id' => $ressourcecompte->membre_id,
            'entreprise_id' => $entreprise->id,
            'paiementstatut_id' => 1, // Payé
            'spotlight' => true,
            'etat' => true,
        ]);

        // 🔗 Récupérer le membre propriétaire du compte ressource
        if ($ressourcecompte->membre) {
            // 🎁 Attribuer récompense de paiement abonnement
            // 💡 Utiliser le montant de l'abonnement comme base pour le calcul en pourcentage
            // ✅ Le membre et l'entreprise sont corrects : propriétaire du compte qui a payé
            $recompenseService->attribuerRecompense('PAIEMENT_ABONNEMENT', $ressourcecompte->membre, $entreprise, $abonnement->id, $abonnement->montant ?? 0);
        }

        return redirect()->route('abonnement.index')
                    ->with('success', 'Abonnement ' . $abonnementtype->titre . ' payé avec succès ! ' . number_format($abonnementtype->montant, 2) . ' XOF débités du compte ressource.');
    }

    public function edit($id)
    {
        $abonnement = Abonnement::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier cet abonnement
        $userId = Auth::id();
        $hasAccess = $abonnement->entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour modifier cet abonnement.');
        }

        $abonnementtypes = Abonnementtype::where('etat', true)->get();

        return view('abonnement.edit', compact('abonnement', 'abonnementtypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'abonnementtype_id' => 'required|exists:abonnementtypes,id',
            'montant' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'date_echeance' => 'required|date|after:date_debut',
            'mode_paiement' => 'nullable|string|max:50',
            'commentaires' => 'nullable|string|max:1000',
        ]);

        $abonnement = Abonnement::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier cet abonnement
        $userId = Auth::id();
        $hasAccess = $abonnement->entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour modifier cet abonnement.');
        }

        $abonnement->update([
            'abonnementtype_id' => $request->abonnementtype_id,
            'montant' => $request->montant,
            'montant_restant' => $request->montant - $abonnement->montant_paye,
            'devise' => $request->devise,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'date_echeance' => $request->date_echeance,
            'mode_paiement' => $request->mode_paiement,
            'commentaires' => $request->commentaires,
        ]);

        return redirect()->route('abonnement.index')
                    ->with('success', 'Abonnement mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $abonnement = Abonnement::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de supprimer cet abonnement
        $userId = Auth::id();
        $hasAccess = $abonnement->entreprise->membres()->where('user_id', $userId)
                                                      ->where('est_membre_cijes', true)
                                                      ->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour supprimer cet abonnement.');
        }

        $abonnement->delete();

        return redirect()->route('abonnement.index')
                    ->with('success', 'Abonnement supprimé avec succès !');
    }

    public function markAsPaid($id)
    {
        $abonnement = Abonnement::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier cet abonnement
        $userId = Auth::id();
        $hasAccess = $abonnement->entreprise->entreprisesmembres()->whereHas('membre', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour modifier cet abonnement.');
        }

        // Si déjà payé, ne rien faire
        if ($abonnement->statut === 'paye') {
            return redirect()->back()->with('info', 'Cet abonnement est déjà payé.');
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

        $montantAPayer = $abonnement->montant_restant;
        if ($ressourceKOBO->solde < $montantAPayer) {
            return redirect()->back()->with('error', 'Solde insuffisant. Montant à payer : ' . number_format($montantAPayer, 2) . ' FCFA. Votre solde actuel : ' . number_format($ressourceKOBO->solde, 2) . ' FCFA.');
        }

        // Vérifier compatibilité ressource ↔ offre (abonnement = offretype_id 6)
        $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourceKOBO->ressourcetype_id)
            ->where('offretype_id', 6) // 6 = abonnements
            ->exists();

        if (!$isCompatible) {
            return redirect()->back()->with('error', "❌ Ce type de ressource ne peut pas payer un abonnement.");
        }

        // Mettre à jour l'abonnement
        $abonnement->update([
            'montant_paye' => $abonnement->montant,
            'montant_restant' => 0,
            'statut' => 'paye',
            'est_a_jour' => true,
            'date_paiement' => now(),
            'mode_paiement' => 'Ressource KOBO',
        ]);

        $reference = 'ABO-' . strtoupper(Str::random(8));

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

        // Créer l'enregistrement abonnementressource
        Abonnementressource::create([
            'montant' => $montantAPayer,
            'reference' => $reference,
            'ressourcecompte_id' => $ressourceKOBO->id,
            'abonnement_id' => $abonnement->id,
            'membre_id' => $membre->id,
            'entreprise_id' => $abonnement->entreprise->id,
            'paiementstatut_id' => 1, // Payé
            'spotlight' => true,
            'etat' => true,
        ]);

        // Mettre à jour le solde du compte KOBO
        $ressourceKOBO->decrement('solde', $montantAPayer);

        return redirect()->route('abonnement.index')
                    ->with('success', 'Abonnement payé avec succès ! ' . number_format($montantAPayer, 2) . ' KOBO débités de votre compte.');
    }
}
