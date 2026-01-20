<?php

namespace App\Http\Controllers;

use App\Models\Espace;
use App\Models\Membre;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Espacetype;
use App\Models\Pays;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use App\Models\Ressourcecompte;
use App\Models\Espaceressource;
use App\Models\Accompagnement;
use App\Models\Reductiontype;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EspaceController extends Controller
{
    public function index()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Afficher uniquement les espaces actifs dans le pays du membre
    $espaces = Espace::with('reservationsAVenir')
        ->with('espacetype')
        ->where('etat', 1)
        ->where('pays_id', $membre->pays_id)
        ->get();

    return view('espace.index', compact('espaces'));
}

    public function show($id)
    {
        $espace = Espace::with('reservationsAVenir')->with('espacetype')->findOrFail($id);
        return view('espace.show', compact('espace'));
    }


public function reserverForm($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $espace = Espace::where('etat', 1)->findOrFail($id);

    // Récupérer les entreprises liées au membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    // Comptes ressources disponibles
    $ressources = Ressourcecompte::where(function ($q) use ($membre, $entrepriseIds) {
            $q->where('membre_id', $membre->id);
            if (!empty($entrepriseIds)) {
                $q->orWhereIn('entreprise_id', $entrepriseIds);
            }
        })
        ->where('etat', 1)
        ->get();

    // Récupérer les accompagnements du membre ou de ses entreprises
    $accompagnements = Accompagnement::where('membre_id', $membre->id)
        ->orWhereIn('entreprise_id', $entrepriseIds)
        ->get();

    // Vérifier les conditions pour autoriser la réservation
    if ($accompagnements->isEmpty() && empty($entrepriseIds)) {
        return redirect()->route('espace.index')
            ->with('error', '⚠️ Vous devez avoir au moins une entreprise ou un accompagnement pour effectuer une réservation.');
    }

    // Déterminer si l'utilisateur doit choisir un accompagnement
    $doitChoisirAccompagnement = $accompagnements->count() > 1;
    $accompagnementAuto = $accompagnements->count() === 1 ? $accompagnements->first() : null;

    return view('espace.reserver', compact('espace', 'ressources', 'accompagnements', 'doitChoisirAccompagnement', 'accompagnementAuto'));
}

/**
 * Récupérer les réductions applicables pour un espace et un membre
 */
private function getReductionsApplicables($membre, $espace)
{
    // Vérifier si le membre est CJES et à jour dans ses cotisations
    if (!$this->membreEstCjesEtAJour($membre)) {
        return collect(); // Retourner une collection vide
    }

    // Récupérer les entreprises du membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    // Vérifier que le membre a au moins une entreprise
    if (empty($entrepriseIds)) {
        return collect(); // Pas d'entreprise = pas de réduction
    }

    // Récupérer toutes les réductions applicables (génériques + spécifiques aux profils des entreprises du membre)
    $toutesReductions = Reductiontype::where('etat', true)
        ->where('offretype_id', 4) // 4 = espaces
        ->where(function($query) use ($entrepriseIds) {
            $query->where('entrepriseprofil_id', 0) // Génériques
                  ->orWhereIn('entrepriseprofil_id', function($subQuery) use ($entrepriseIds) {
                      // Récupérer les profils des entreprises du membre
                      return $subQuery->from('entreprises')
                          ->select('entrepriseprofil_id')
                          ->whereIn('id', $entrepriseIds)
                          ->whereNotNull('entrepriseprofil_id');
                  });
        })
        ->where(function($query) use ($membre, $entrepriseIds) {
            $query->whereNull('date_debut')
                  ->orWhere(function($subQuery) {
                      $subQuery->where('date_debut', '<=', now())
                            ->where('date_fin', '>=', now());
                  });
        })
        ->orderBy('pourcentage', 'desc')
        ->orderBy('montant', 'desc')
        ->get();

    // Trouver la meilleure réduction active
    $meilleureReduction = null;
    $meilleureEconomie = 0;

    foreach ($toutesReductions as $reduction) {
        if ($reduction->isPromotionActive()) {
            // Calculer l'économie potentielle sur un prix de base de 10000 XOF
            $prixBase = 10000;
            $economie = $reduction->calculateReduction($prixBase);
            
            if ($economie > $meilleureEconomie) {
                $meilleureEconomie = $economie;
                $meilleureReduction = $reduction;
            }
        }
    }

    // Retourner seulement la meilleure réduction
    return $meilleureReduction ? collect([$meilleureReduction]) : collect();
}

/**
 * Vérifier si le membre est CJES et à jour dans ses cotisations
 */
private function membreEstCjesEtAJour($membre)
{
    // Récupérer les entreprises du membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    // Vérifier que le membre a au moins une entreprise
    if (empty($entrepriseIds)) {
        return false;
    }

    // Récupérer uniquement les entreprises CJES du membre
    $entreprisesCjes = \App\Models\Entreprise::whereIn('id', $entrepriseIds)
        ->where('est_membre_cijes', true)
        ->get();

    if ($entreprisesCjes->isEmpty()) {
        return false; // Aucune entreprise CJES
    }

    // Vérifier si au moins une entreprise CJES est à jour dans ses cotisations
    $cotisationValide = \App\Models\Cotisation::whereIn('entreprise_id', $entreprisesCjes->pluck('id'))
        ->where('statut', 'paye')
        ->where('est_a_jour', true)
        ->where('date_fin', '>=', now())
        ->exists();

    return $cotisationValide;
}

/**
 * Calculer le meilleur montant avec réduction
 */
private function calculerMontantAvecReduction($montantOriginal, $reductions)
{
    $meilleurMontant = $montantOriginal;
    $meilleureReduction = null;

    foreach ($reductions as $reduction) {
        if ($reduction->isPromotionActive()) {
            $montantAvecReduction = $reduction->getPrixAvecReduction($montantOriginal);
            
            if ($montantAvecReduction < $meilleurMontant) {
                $meilleurMontant = $montantAvecReduction;
                $meilleureReduction = $reduction;
            }
        }
    }

    return [
        'montant_final' => $meilleurMontant,
        'montant_original' => $montantOriginal,
        'reduction' => $meilleureReduction,
        'economie' => $montantOriginal - $meilleurMontant
    ];
}


public function reserverStore(Request $request, $id)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    $espace = Espace::where('etat', 1)->findOrFail($id);

    // Récupérer les réductions applicables pour cet espace
    $reductions = $this->getReductionsApplicables($membre, $espace);

    $montantOriginal = $request->input('montant') !== null 
        ? (float) $request->input('montant') 
        : (float) ($espace->prix ?? 0);

    // Calculer le montant avec la meilleure réduction
    $calculReduction = $this->calculerMontantAvecReduction($montantOriginal, $reductions);
    $montant = $calculReduction['montant_final'];
    
    $accompagnementId = $request->input('accompagnement_id');

    // Si aucun accompagnement n'est fourni, essayer de le récupérer automatiquement
    if (!$accompagnementId) {
        $accompagnements = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entrepriseIds)
            ->get();
        
        if ($accompagnements->count() === 1) {
            $accompagnementId = $accompagnements->first()->id;
        }
    }

    $rules = [
        'montant' => 'nullable|numeric|min:0',
        'datedebut' => 'required|date|after_or_equal:today',
        'datefin' => 'required|date|after:datedebut',
        'accompagnement_id' => 'nullable|exists:accompagnements,id',
    ];
    if ($montant > 0) {
        $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
    }
    $request->validate($rules);

    // Vérifier si déjà réservé sur la même période
    $conflict = Reservation::where('espace_id', $espace->id)
        ->where(function ($q) use ($request) {
            $q->whereBetween('datedebut', [$request->datedebut, $request->datefin])
              ->orWhereBetween('datefin', [$request->datedebut, $request->datefin]);
        })
        ->exists();

    if ($conflict) {
        return back()->withInput()->with('error', '⚠️ Cet espace est déjà réservé sur cette période.');
    }

    $ressourcecompte = null;
    if ($montant > 0) {
        $ressourcecompte = Ressourcecompte::where('id', $request->ressourcecompte_id)
            ->where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id)
                    ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->firstOrFail();
    }

    DB::beginTransaction();

    try {
        $reference = 'PAI-ESP-' . strtoupper(Str::random(8));

        if ($montant > 0) {
            // Vérifier compatibilité ressource ↔ offre (espace = offretype_id 4 par ex.)
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 4)
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer un espace.");
            }

            if ($ressourcecompte->solde < $montant) {
                throw new \Exception("⚠️ Solde insuffisant dans ce compte ressource.");
            }

            // Débit du compte payeur
            Ressourcetransaction::create([
                'montant' => -$montant,
                'reference' => $reference,
                'ressourcecompte_id' => $ressourcecompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 2, // débit
                'entreprise_id' => $ressourcecompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $ressourcecompte->decrement('solde', $montant);
        }

        // Enregistrer la trace du paiement (espaceressource)
        Espaceressource::create([
            'montant' => $montant,
            'reference' => $reference,
            'accompagnement_id' => $accompagnementId ?? null,
            'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
            'espace_id' => $espace->id,
            'paiementstatut_id' => 1, // 1 = payé
            'membre_id' => $membre->id,
            'entreprise_id' => $ressourcecompte->entreprise_id ?? null,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        // Créer la réservation
        $statutDefaut = Reservationstatut::where('etat', 1)->first();
        Reservation::create([
            'membre_id' => $membre->id,
            'espace_id' => $espace->id,
            'datedebut' => $request->datedebut,
            'datefin' => $request->datefin,
            'observation' => $request->observation,
            'reservationstatut_id' => $statutDefaut?->id,
            'etat' => 1,
            'spotlight' => 0,
        ]);

        DB::commit();

        // Préparer le message de succès avec informations de réduction
        $messageSuccess = '✅ Réservation réussie.';
        if ($montant > 0) {
            if ($calculReduction['reduction']) {
                $economie = number_format($calculReduction['economie'], 2);
                $reductionTitre = $calculReduction['reduction']->titre_complet;
                $messageSuccess = "✅ Réservation et paiement réussis avec réduction : {$reductionTitre} (Économie : {$economie} XOF)";
            } else {
                $messageSuccess = '✅ Réservation et paiement réussis.';
            }
        } else {
            $messageSuccess = '✅ Réservation gratuite réussie.';
        }

        return redirect()->route('espace.index')
            ->with('success', $messageSuccess);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
    }
}



}
