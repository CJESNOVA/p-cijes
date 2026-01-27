<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\Prestationtype;
use App\Models\Pays;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Prestationrealisee;
use App\Models\Prestationrealiseestatut;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use App\Models\Ressourcecompte;
use App\Models\Prestationressource;
use App\Models\Accompagnement;
use App\Models\Reductiontype;
use App\Models\Cotisation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrestationController extends Controller
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

        // Récupère toutes les entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Récupère les prestations liées uniquement à ces entreprises
        $prestations = Prestation::whereIn('entreprise_id', $entrepriseIds)
            ->with(['prestationtype', 'entreprise'])
            ->orderByDesc('id')
            ->get();

        return view('prestation.index', compact('prestations'));
    }


    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->get();

        $prestationtypes = Prestationtype::where('etat', 1)->get();

        return view('prestation.form', [
            'prestation' => null,
            'prestationtypes' => $prestationtypes,
            'pays_id' => $membre->pays_id,
            'entreprises' => $entreprises,
        ]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();
        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->get();

        $prestation = Prestation::/*where('entreprise_id', $entreprise->id)->*/findOrFail($id);
        $prestationtypes = Prestationtype::where('etat', 1)->get();

        return view('prestation.form', compact('prestation', 'prestationtypes', 'entreprises'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();

        $validated = $this->validateData($request);
        //$validated['entreprise_id'] = $entreprises->id;
        $validated['pays_id'] = $membre->pays_id;
        $validated['etat'] = 1;
        $validated['spotlight'] = $request->has('spotlight') ? 1 : 0;

        Prestation::create($validated);

        return redirect()->route('prestation.index')->with('success', 'Prestation créée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();

        $prestation = Prestation::findOrFail($id);//where('entreprise_id', $entreprise->id)->

        $validated = $this->validateData($request);
        //$validated['spotlight'] = $request->has('spotlight') ? 1 : 0;

        $prestation->update($validated);

        return redirect()->route('prestation.index')->with('success', 'Prestation mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();
        //$entreprises = Entreprise::where('membre_id', $membre->id)->get();

        $prestation = Prestation::findOrFail($id);//where('entreprise_id', $entreprise->id)->
        $prestation->delete();

        return redirect()->route('prestation.index')->with('success', 'Prestation supprimée avec succès.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'prix' => 'required|numeric|min:0',
            'duree' => 'required|string|max:100',
            'prestationtype_id' => 'required|exists:prestationtypes,id',
            'titre' => 'nullable|string',
            'description' => 'nullable|string',
            'entreprise_id' => 'nullable|string',
        ]);
    }

    // Liste des prestations ouvertes
    public function liste()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer toutes les entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Récupérer les prestations du pays du membre ou de ses entreprises
        $prestations = Prestation::with(['prestationtype', 'entreprise'])
            ->where('etat', 1)
            ->where(function($q) use ($membre, $entrepriseIds) {
                $q->where('pays_id', $membre->pays_id)
                ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->orderByDesc('id')
            ->get();

        $membreId = $membre->id;

        return view('prestation.liste', compact('prestations', 'membreId'));
    }

    public function inscrireForm($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
    }

    $prestation = Prestation::where('etat', 1)->findOrFail($id);

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

    return view('prestation.inscrire', compact('prestation', 'ressources', 'optionsPaiement'));
}


public function inscrireStore(Request $request, $id)
{
    $membre = Membre::where('user_id', Auth::id())->firstOrFail();
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    $prestation = Prestation::where('etat', 1)->findOrFail($id);

    // Récupérer le contexte de paiement choisi
    $contextePaiement = null;
    $contexteType = $request->input('contexte_type'); // 'entreprise', 'accompagnement', 'membre'
    $contexteId = null;

    // Récupérer l'ID selon le type de contexte
    switch ($contexteType) {
        case 'entreprise':
            $contexteId = $request->input('entreprise_id');
            break;
        case 'accompagnement':
            $contexteId = $request->input('accompagnement_id');
            break;
        case 'membre':
            $contexteId = $request->input('membre_id');
            break;
    }

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
    $reductions = $this->getReductionsPourContexte($contextePaiement, $prestation);

    $montantOriginal = $request->input('montant') !== null 
        ? (float) $request->input('montant') 
        : (float) ($prestation->prix ?? 0);

    // Calculer le montant avec la meilleure réduction
    $calculReduction = $this->calculerMontantAvecReduction($montantOriginal, $reductions);
    $montant = $calculReduction['montant_final'];

    // Validation des règles
    $rules = [
        'contexte_type' => 'required|in:entreprise,accompagnement,membre',
    ];

    if ($montant > 0) {
        $rules['ressourcecompte_id'] = 'required|exists:ressourcecomptes,id';
    }

    $request->validate($rules);

    // Vérifier si déjà réalisée via cet accompagnement (si accompagnement)
    if ($contextePaiement['type'] === 'accompagnement_entreprise' || $contextePaiement['type'] === 'accompagnement_membre') {
        if (Prestationrealisee::where('prestation_id', $prestation->id)
            ->where('accompagnement_id', $contextePaiement['id'])
            ->exists()
        ) {
            return back()->with('error', '⚠️ Cette prestation a déjà été enregistrée pour cet accompagnement.');
        }
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

    // Receveur : entreprise propriétaire de la prestation
    $receveurEntrepriseId = $prestation->entreprise_id;
    $receveurCompte = Ressourcecompte::firstOrCreate(
        ['entreprise_id' => $receveurEntrepriseId, 'ressourcetype_id' => 1],
        ['membre_id' => null, 'solde' => 0, 'etat' => 1, 'spotlight' => 0]
    );

    DB::beginTransaction();

    try {
        $reference = 'PAI-PREST-' . strtoupper(Str::random(8));

        if ($montant > 0) {
            // Vérifier compatibilité ressource ↔ prestation
            $isCompatible = Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
                ->where('offretype_id', 2) // 2 = prestations
                ->exists();

            if (!$isCompatible) {
                throw new \Exception("❌ Ce type de ressource ne peut pas payer une prestation.");
            }

            if ($ressourcecompte->solde < $montant) {
                throw new \Exception("⚠️ Solde insuffisant. Montant requis: {$montant} FCFA, Solde disponible: {$ressourcecompte->solde} FCFA");
            }

            // Débit
            Ressourcetransaction::create([
                'montant' => -$montant,
                'reference' => $reference,
                'ressourcecompte_id' => $ressourcecompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 2,
                'entreprise_id' => $ressourcecompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $ressourcecompte->decrement('solde', $montant);

            // Crédit
            Ressourcetransaction::create([
                'montant' => $montant,
                'reference' => $reference,
                'ressourcecompte_id' => $receveurCompte->id,
                'datetransaction' => now(),
                'operationtype_id' => 1,
                'entreprise_id' => $receveurCompte->entreprise_id,
                'spotlight' => 0,
                'etat' => 1,
            ]);
            $receveurCompte->increment('solde', $montant);
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

        // Enregistrer la trace du paiement (prestationressource)
        Prestationressource::create([
            'montant' => $montant,
            'reference' => $reference,
            'accompagnement_id' => $accompagnementId,
            'ressourcecompte_id' => $montant > 0 ? $ressourcecompte->id : null,
            'prestation_id' => $prestation->id,
            'paiementstatut_id' => 1, // 1 = payé (paiement unique) ou gratuit
            'membre_id' => $membre->id,
            'entreprise_id' => $entrepriseId,
            'spotlight' => 0,
            'etat' => 1,
        ]);

        // Créer la prestation réalisée
        $statutDefaut = Prestationrealiseestatut::where('etat', 1)->first();
        Prestationrealisee::create([
            'prestation_id' => $prestation->id,
            'accompagnement_id' => $accompagnementId,
            'daterealisation' => now(),
            'prestationrealiseestatut_id' => $statutDefaut?->id,
            'note' => '',
            'feedback' => '',
            'spotlight' => 0,
            'etat' => 1,
        ]);

        DB::commit();

        // Message de succès selon le type d'inscription
        if ($montant > 0) {
            $message = "✅ Inscription à la prestation '{$prestation->titre}' bien effectuée ! Paiement de " . number_format($montant, 2) . " FCFA effectué.";
        } else {
            $message = "✅ Inscription à la prestation '{$prestation->titre}' bien effectuée ! Aucun paiement requis.";
        }

        // Ajouter les informations sur les réductions si applicable
        if ($montantOriginal > $montant) {
            $economie = $montantOriginal - $montant;
            $pourcentage = round(($economie / $montantOriginal) * 100, 1);
            $message .= " 🎉 Vous avez économisé " . number_format($economie, 2) . " FCFA ({$pourcentage}% de réduction) !";
        }

        return redirect()->route('prestation.liste')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', '⚠️ Erreur : ' . $e->getMessage());
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
    private function getReductionsPourContexte($contexte, $prestation)
    {
        $reductions = collect();
        
        // Si c'est une entreprise CJES à jour
        if ($contexte['type'] === 'entreprise' || $contexte['type'] === 'accompagnement_entreprise') {
            if ($contexte['est_cjes'] && $contexte['cotisation_a_jour'] && $contexte['profil_id']) {
                $reductions = Reductiontype::where('etat', true)
                    ->where('offretype_id', 2) // 2 = prestations
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
            ->where('offretype_id', 2) // 2 = prestations
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

    /**
     * Calculer le montant avec réductions pour une prestation
     */
    public function calculerMontant(Request $request, $id)
    {
        $prestation = Prestation::findOrFail($id);
        $contexte = $request->input('contexte');
        $contexteId = $request->input('contexte_id');
        $prixBase = $request->input('prix_base');
        $quantite = $request->input('quantite', 1); // Quantité par défaut

        try {
            // Calculer le montant de base
            $montantBase = $prixBase * $quantite;
            
            // Récupérer les réductions applicables
            $reductions = $this->getReductionsPourContexte($contexte, $prestation);
            
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
                'quantite' => $quantite
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du montant: ' . $e->getMessage()
            ], 500);
        }
    }


}
