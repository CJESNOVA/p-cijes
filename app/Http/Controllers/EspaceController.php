<?php

namespace App\Http\Controllers;

use App\Models\Espace;
use App\Models\Membre;
use App\Models\Reservation;
use App\Models\Reservationstatut;
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
use App\Models\Cotisation;
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
            ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
    }

    $espace = Espace::where('etat', 1)->findOrFail($id);

    // Récupérer toutes les options de paiement pour le membre
    $optionsPaiement = $this->getOptionsPaiementPourMembre($membre);
    
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

    // Déterminer si l'utilisateur doit choisir
    $doitChoisirPaiement = false;
    $contexteAuto = null;
    
    // Logique de sélection automatique
    if (count($optionsPaiement['accompagnements'] ?? []) === 1) {
        $contexteAuto = $optionsPaiement['accompagnements'][0];
    } elseif (count($optionsPaiement['entreprises'] ?? []) === 1 && empty($optionsPaiement['accompagnements'])) {
        $contexteAuto = $optionsPaiement['entreprises'][0];
    } elseif (empty($optionsPaiement['entreprises']) && empty($optionsPaiement['accompagnements'])) {
        $contexteAuto = $optionsPaiement['membre'];
    } else {
        $doitChoisirPaiement = true;
    }

    // Calculer les réductions pour le contexte automatique
    $reductionsApplicables = $contexteAuto ? $this->getReductionsPourContexte($contexteAuto, $espace) : collect();

    return view('espace.reserver', compact(
        'espace', 
        'ressources', 
        'accompagnements', 
        'optionsPaiement',
        'doitChoisirPaiement',
        'contexteAuto',
        'reductionsApplicables'
    ));
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
     * Récupérer toutes les options de paiement pour le membre
     */
    private function getOptionsPaiementPourMembre($membre)
    {
        $options = [];
        
        // Récupérer les entreprises du membre
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->with('entreprise')
            ->get();
        
        // Ajouter les entreprises comme options
        foreach ($entreprises as $entreprisemembre) {
            $options['entreprises'][] = [
                'id' => $entreprisemembre->entreprise_id,
                'nom' => $entreprisemembre->entreprise->nom,
                'type' => 'entreprise',
                'est_cjes' => $entreprisemembre->entreprise->est_membre_cijes,
                'profil_id' => $entreprisemembre->entreprise->entrepriseprofil_id,
                'cotisation_a_jour' => $this->verifierCotisationsEntreprise($entreprisemembre->entreprise_id)
            ];
        }
        
        // Récupérer les accompagnements du membre
        $accompagnements = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entreprises->pluck('entreprise_id'))
            ->with(['entreprise', 'membre'])
            ->get();
        
        // Ajouter les accompagnements comme options
        foreach ($accompagnements as $accompagnement) {
            if ($accompagnement->entreprise_id) {
                // Accompagnement d'entreprise
                $entreprise = $accompagnement->entreprise;
                $options['accompagnements'][] = [
                    'id' => $accompagnement->id,
                    'nom' => "Accompagnement - " . $entreprise->nom,
                    'entreprise_nom' => $entreprise->nom,
                    'type' => 'accompagnement_entreprise',
                    'entreprise_id' => $entreprise->id,
                    'est_cjes' => $entreprise->est_membre_cijes,
                    'profil_id' => $entreprise->entrepriseprofil_id,
                    'cotisation_a_jour' => $this->verifierCotisationsEntreprise($entreprise->id)
                ];
            } else {
                // Accompagnement de membre
                $options['accompagnements'][] = [
                    'id' => $accompagnement->id,
                    'nom' => "Accompagnement - " . $accompagnement->membre->nom_complet,
                    'membre_nom' => $accompagnement->membre->nom_complet,
                    'type' => 'accompagnement_membre',
                    'entreprise_id' => null,
                    'est_cjes' => false,
                    'profil_id' => null,
                    'cotisation_a_jour' => false
                ];
            }
        }
        
        // Ajouter l'option membre seul
        $options['membre'] = [
            'id' => $membre->id,
            'nom' => $membre->nom_complet,
            'type' => 'membre',
            'est_cjes' => false,
            'profil_id' => null,
            'cotisation_a_jour' => false
        ];
        
        return $options;
    }

    /**
     * Vérifier si une entreprise est à jour dans ses cotisations
     */
    private function verifierCotisationsEntreprise($entrepriseId)
    {
        $entreprise = Entreprise::find($entrepriseId);
        
        if (!$entreprise || !$entreprise->est_membre_cijes) {
            return false;
        }
        
        return Cotisation::where('entreprise_id', $entrepriseId)
            ->where('statut', 'paye')
            ->where('est_a_jour', true)
            ->where('date_fin', '>=', now())
            ->exists();
    }

    /**
     * Calculer les réductions selon le contexte de paiement
     */
    private function getReductionsPourContexte($contexte, $espace)
    {
        $reductions = collect();
        
        // Si c'est une entreprise CJES à jour
        if ($contexte['type'] === 'entreprise' || $contexte['type'] === 'accompagnement_entreprise') {
            if ($contexte['est_cjes'] && $contexte['cotisation_a_jour'] && $contexte['profil_id']) {
                $reductions = Reductiontype::where('etat', true)
                    ->where('offretype_id', 4) // 4 = espaces
                    ->where('entrepriseprofil_id', $contexte['profil_id'])
                    ->where(function($query) {
                        $query->whereNull('date_debut')
                              ->orWhere(function($subQuery) {
                                  $subQuery->where('date_debut', '<=', now())
                                        ->where('date_fin', '>=', now());
                              });
                    })
                    ->get();
            }
        }
        
        // Ajouter les réductions génériques (profil_id = 0)
        $reductionsGeneriques = Reductiontype::where('etat', true)
            ->where('offretype_id', 4) // 4 = espaces
            ->where('entrepriseprofil_id', 0) // Génériques
            ->where(function($query) {
                $query->whereNull('date_debut')
                      ->orWhere(function($subQuery) {
                          $subQuery->where('date_debut', '<=', now())
                                ->where('date_fin', '>=', now());
                      });
            })
            ->get();
        
        return $reductions->merge($reductionsGeneriques);
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
    $espace = Espace::where('etat', 1)->findOrFail($id);

    // Récupérer le contexte de paiement choisi
    $contextePaiement = null;
    $contexteType = $request->input('contexte_type'); // 'entreprise', 'accompagnement', 'membre'
    $contexteId = $request->input('contexte_id');

    if ($contexteType && $contexteId) {
        $optionsPaiement = $this->getOptionsPaiementPourMembre($membre);
        
        switch ($contexteType) {
            case 'entreprise':
                $contextePaiement = collect($optionsPaiement['entreprises'] ?? [])->firstWhere('id', $contexteId);
                break;
            case 'accompagnement':
                $contextePaiement = collect($optionsPaiement['accompagnements'] ?? [])->firstWhere('id', $contexteId);
                break;
            case 'membre':
                $contextePaiement = $optionsPaiement['membre'];
                break;
        }
    }

    // Si pas de contexte choisi, utiliser la logique automatique
    if (!$contextePaiement) {
        $optionsPaiement = $this->getOptionsPaiementPourMembre($membre);
        
        if (count($optionsPaiement['accompagnements'] ?? []) === 1) {
            $contextePaiement = $optionsPaiement['accompagnements'][0];
        } elseif (count($optionsPaiement['entreprises'] ?? []) === 1 && empty($optionsPaiement['accompagnements'])) {
            $contextePaiement = $optionsPaiement['entreprises'][0];
        } else {
            $contextePaiement = $optionsPaiement['membre'];
        }
    }

    // Calculer les réductions selon le contexte
    $reductions = $this->getReductionsPourContexte($contextePaiement, $espace);
    
    $montantOriginal = $request->input('montant') !== null 
        ? (float) $request->input('montant') 
        : (float) ($espace->prix ?? 0);

    // Calculer le montant avec la meilleure réduction
    $calculReduction = $this->calculerMontantAvecReduction($montantOriginal, $reductions);
    $montant = $calculReduction['montant_final'];

    // Validation des règles
    $rules = [
        'datedebut' => 'required|date|after_or_equal:today',
        'datefin' => 'required|date|after:datedebut',
        'contexte_type' => 'required|in:entreprise,accompagnement,membre',
        'contexte_id' => 'required|integer',
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

    // Récupérer les entreprises du membre pour la validation des ressources
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

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

        // Paiement unique : le montant doit être payé en totalité
        if ($montant > 0) {
            // Vérifier compatibilité ressource ↔ offre (espace = offretype_id 4)
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 4)
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer un espace.");
            }

            if ($ressourcecompte->solde < $montant) {
                throw new \Exception("⚠️ Solde insuffisant. Montant requis: {$montant} FCFA, Solde disponible: {$ressourcecompte->solde} FCFA");
            }

            // Débit du compte payeur (paiement unique en totalité)
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

        // Déterminer les IDs pour l'enregistrement
        $accompagnementId = null;
        $entrepriseId = null;
        
        if ($contextePaiement['type'] === 'accompagnement_entreprise' || $contextePaiement['type'] === 'accompagnement_membre') {
            $accompagnementId = $contextePaiement['id'];
            $entrepriseId = $contextePaiement['entreprise_id'] ?? null;
        } elseif ($contextePaiement['type'] === 'entreprise') {
            $entrepriseId = $contextePaiement['id'];
        }

        // Enregistrer la trace du paiement (espaceressource) - paiement unique ou gratuit
        Espaceressource::create([
            'montant' => $montant,
            'reference' => $reference,
            'accompagnement_id' => $accompagnementId,
            'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
            'espace_id' => $espace->id,
            'paiementstatut_id' => 1, // 1 = payé (paiement unique) ou gratuit
            'membre_id' => $membre->id,
            'entreprise_id' => $entrepriseId,
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
            'reservationstatut_id' => $statutDefaut->id ?? 1,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        DB::commit();

        // Message de succès selon le type de réservation
        if ($montant > 0) {
            $message = "✅ Réservation confirmée ! Paiement unique de {$montant} FCFA effectué.";
        } else {
            $message = "✅ Réservation gratuite confirmée ! Aucun paiement requis.";
        }

        if (isset($calculReduction['reduction']) && $calculReduction['reduction']) {
            $economie = $calculReduction['economie'];
            $message .= " Économie réalisée : {$economie} FCFA.";
        }

        return redirect()->route('espace.show', $espace->id)
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
    }
}

/**
 * Calculer le montant avec réduction via AJAX
 */
public function calculerMontant(Request $request, $id)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();
    $espace = Espace::where('etat', 1)->findOrFail($id);

    $contexteType = $request->input('contexte_type');
    $contexteId = $request->input('contexte_id');
    $montantOriginal = (float) $request->input('montant', $espace->prix ?? 0);

    // Récupérer le contexte de paiement
    $contextePaiement = null;
    if ($contexteType && $contexteId) {
        $optionsPaiement = $this->getOptionsPaiementPourMembre($membre);
        
        switch ($contexteType) {
            case 'entreprise':
                $contextePaiement = collect($optionsPaiement['entreprises'] ?? [])->firstWhere('id', $contexteId);
                break;
            case 'accompagnement':
                $contextePaiement = collect($optionsPaiement['accompagnements'] ?? [])->firstWhere('id', $contexteId);
                break;
            case 'membre':
                $contextePaiement = $optionsPaiement['membre'];
                break;
        }
    }

    if (!$contextePaiement) {
        return response()->json([
            'error' => 'Contexte de paiement non valide'
        ], 400);
    }

    // Calculer les réductions
    $reductions = $this->getReductionsPourContexte($contextePaiement, $espace);
    $calculReduction = $this->calculerMontantAvecReduction($montantOriginal, $reductions);

    return response()->json([
        'montant_original' => $montantOriginal,
        'montant_final' => $calculReduction['montant_final'],
        'economie' => $calculReduction['economie'],
        'reduction' => $calculReduction['reduction'] ? [
            'titre' => $calculReduction['reduction']->titre,
            'pourcentage' => $calculReduction['reduction']->pourcentage,
            'montant' => $calculReduction['reduction']->montant,
        ] : null,
        'contexte' => [
            'nom' => $contextePaiement['nom'],
            'type' => $contextePaiement['type'],
            'est_cjes' => $contextePaiement['est_cjes'],
            'cotisation_a_jour' => $contextePaiement['cotisation_a_jour'],
            'profil_id' => $contextePaiement['profil_id'],
        ]
    ]);
}


}
