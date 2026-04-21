<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Membre;
use App\Models\Evenementinscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evenementtype;
use App\Models\Pays;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use App\Models\Ressourcecompte;
use App\Models\Evenementressource;
use App\Models\Accompagnement;
use App\Models\Reductiontype;
use App\Models\Cotisation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EvenementController extends Controller
{
    // Liste des événements disponibles
    public function index()
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $evenements = Evenement::with(['evenementtype'])
        ->where('etat', 1)
        ->where('pays_id', $membre->pays_id)
        ->whereDate('dateevenement', '>=', now())
        ->orderBy('dateevenement', 'asc')
        ->get();

    // Récupérer les inscriptions de l'utilisateur
    $inscriptions = Evenementinscription::where('membre_id', $membre->id)
        ->where('etat', 1)
        ->pluck('evenement_id')
        ->toArray();

    return view('evenement.index', compact('evenements', 'inscriptions'));
}



    // Détails d’un événement
    public function show(Evenement $evenement)
    {
        $membre = Membre::where('user_id', Auth::id())->firstOrFail();

        $dejaInscrit = null;

        if ($membre) {
            $dejaInscrit = $evenement->inscriptions()
                ->where('membre_id', $membre->id)
                ->with('evenementinscriptiontype') // charger la relation pour Blade
                ->first(); // récupérer l'objet inscription ou null
        }

        return view('evenement.show', compact('evenement', 'dejaInscrit'));
    }



    public function inscrireForm($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    $evenement = Evenement::where('etat', 1)->findOrFail($id);

    // Récupérer les options de paiement du membre
    $optionsPaiement = $this->getOptionsPaiementPourMembre($membre);

    // Récupérer les entreprises du membre pour les ressources
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

    return view('evenement.inscrire', compact('evenement', 'ressources', 'optionsPaiement'));
}


public function inscrireStore(Request $request, $id)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    $evenement = Evenement::where('etat', 1)->findOrFail($id);

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
    $reductions = $this->getReductionsPourContexte($contextePaiement, $evenement);

    $montantOriginal = $request->input('montant') !== null 
        ? (float) $request->input('montant') 
        : (float) ($evenement->prix ?? 0);

    // Calculer le montant avec la meilleure réduction
    $calculReduction = $this->calculerMontantAvecReduction($montantOriginal, $reductions);
    $montant = $calculReduction['montant_final'];

    // Validation des règles
    $rules = [
        'contexte_type' => 'required|in:entreprise,accompagnement,membre',
        'contexte_id' => 'required|integer',
    ];

    if ($montant > 0) {
        $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
    }

    $request->validate($rules);

    // Vérifier si déjà inscrit
    if (Evenementinscription::where('membre_id', $membre->id)->where('evenement_id', $evenement->id)->exists()) {
        return back()->withInput()->with('error', '⚠️ Vous êtes déjà inscrit à cet événement.');
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
        $reference = 'PAI-EVT-' . strtoupper(Str::random(8));

        // Paiement unique : le montant doit être payé en totalité
        if ($montant > 0) {
            // Vérifier compatibilité ressource ↔ offre (événement = offretype_id 3)
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 3)
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer un événement.");
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

        // Enregistrer la trace du paiement (evenementressource) - paiement unique ou gratuit
        Evenementressource::create([
            'montant' => $montant,
            'reference' => $reference,
            'accompagnement_id' => $accompagnementId,
            'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
            'evenement_id' => $evenement->id,
            'paiementstatut_id' => 1, // 1 = payé (paiement unique) ou gratuit
            'membre_id' => $membre->id,
            'entreprise_id' => $entrepriseId,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        // Créer l'inscription
        $statutDefaut = \App\Models\Evenementinscriptiontype::where('etat', 1)->first();
        Evenementinscription::create([
            'membre_id' => $membre->id,
            'evenement_id' => $evenement->id,
            'dateevenementinscription' => now(),
            'evenementinscriptiontype_id' => $statutDefaut?->id,
            'etat' => 1,
            'spotlight' => 0,
        ]);

        DB::commit();

        // Message de succès selon le type d'inscription
        if ($montant > 0) {
            $message = "✅ Inscription confirmée ! Paiement unique de {$montant} FCFA effectué.";
        } else {
            $message = "✅ Inscription gratuite confirmée ! Aucun paiement requis.";
        }

        if (isset($calculReduction['reduction']) && $calculReduction['reduction']) {
            $economie = $calculReduction['economie'];
            $message .= " Économie réalisée : {$economie} FCFA.";
        }

        return redirect()->route('evenement.show', $evenement->id)
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', '⚠️ Erreur : ' . $e->getMessage());
    }
}

    /**
     * Calculer le montant avec réductions pour un événement
     */
    public function calculerMontant(Request $request, $id)
    {
        $evenement = Evenement::findOrFail($id);
        $contexte = $request->input('contexte');
        $contexteId = $request->input('contexte_id');
        $prixBase = $request->input('prix_base');
        $duree = $request->input('duree', 1); // Pour un événement, la durée est toujours 1

        try {
            // Calculer le montant de base
            $montantBase = $prixBase * $duree;
            
            // Récupérer les réductions applicables
            $reductions = $this->getReductionsPourContexte($contexte, $contexteId);
            
            // Appliquer la meilleure réduction
            $calculReduction = $this->calculerMontantAvecReduction($montantBase, $reductions);
            $montantFinal = $calculReduction['montant_final'];
            
            // Calculer le montant de la réduction
            $reductionMontant = $montantBase - $montantFinal;
            
            return response()->json([
                'success' => true,
                'montant_base' => $montantBase,
                'montant_final' => $montantFinal,
                'reduction' => $reductionMontant,
                'reduction_pourcentage' => $reductionMontant > 0 ? round(($reductionMontant / $montantBase) * 100, 2) : 0,
                'reduction_description' => $reductionMontant > 0 ? 'Réduction appliquée' : null,
                'duree' => $duree
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du montant: ' . $e->getMessage()
            ], 500);
        }
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
            'nom' => $membre->prenom . ' ' . $membre->nom,
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
        return Cotisation::where('entreprise_id', $entrepriseId)
            ->where('statut', 'paye')
            ->where('est_a_jour', true)
            ->where('date_fin', '>=', now())
            ->exists();
    }

    /**
     * Calculer les réductions selon le contexte de paiement
     */
    private function getReductionsPourContexte($contexte, $evenement)
    {
        $reductions = collect();
        
        // Si c'est une entreprise CJES à jour
        if ($contexte['type'] === 'entreprise' || $contexte['type'] === 'accompagnement_entreprise') {
            if ($contexte['est_cjes'] && $contexte['cotisation_a_jour'] && $contexte['profil_id']) {
                $reductions = Reductiontype::where('etat', true)
                    ->where('offretype_id', 3) // 3 = événements
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
            ->where('offretype_id', 3) // 3 = événements
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
            'meilleure_reduction' => $meilleureReduction
        ];
    }
}
