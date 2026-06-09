<?php

namespace App\Services;

use App\Models\Accompagnement;
use App\Models\Cotisation;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Reductiontype;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcetypeoffretype;
use Illuminate\Support\Str;

class PaiementService
{
    /**
     * Récupérer toutes les options de paiement pour un membre.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController
     */
    public function getOptionsPaiementPourMembre($membre): array
    {
        $options = [];

        $entreprises = Entreprisemembre::where('membre_id', $membre->id)
            ->with('entreprise')
            ->get();

        foreach ($entreprises as $entreprisemembre) {
            $options['entreprises'][] = [
                'id' => $entreprisemembre->entreprise_id,
                'nom' => $entreprisemembre->entreprise->nom,
                'type' => 'entreprise',
                'est_cjes' => $entreprisemembre->entreprise->est_membre_cijes,
                'profil_id' => $entreprisemembre->entreprise->entrepriseprofil_id,
                'cotisation_a_jour' => $this->verifierCotisationsEntreprise($entreprisemembre->entreprise_id),
            ];
        }

        $accompagnements = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entreprises->pluck('entreprise_id'))
            ->with(['entreprise', 'membre'])
            ->get();

        foreach ($accompagnements as $accompagnement) {
            if ($accompagnement->entreprise_id) {
                $entreprise = $accompagnement->entreprise;
                $options['accompagnements'][] = [
                    'id' => $accompagnement->id,
                    'nom' => "Accompagnement - " . $entreprise->nom,
                    'entreprise_nom' => $entreprise->nom,
                    'type' => 'accompagnement_entreprise',
                    'entreprise_id' => $entreprise->id,
                    'est_cjes' => $entreprise->est_membre_cijes,
                    'profil_id' => $entreprise->entrepriseprofil_id,
                    'cotisation_a_jour' => $this->verifierCotisationsEntreprise($entreprise->id),
                ];
            } else {
                $options['accompagnements'][] = [
                    'id' => $accompagnement->id,
                    'nom' => "Accompagnement - " . ($accompagnement->membre->nom_complet ?? $accompagnement->membre->prenom . ' ' . $accompagnement->membre->nom),
                    'membre_nom' => $accompagnement->membre->nom_complet ?? $accompagnement->membre->prenom . ' ' . $accompagnement->membre->nom,
                    'type' => 'accompagnement_membre',
                    'entreprise_id' => null,
                    'est_cjes' => false,
                    'profil_id' => null,
                    'cotisation_a_jour' => false,
                ];
            }
        }

        $options['membre'] = [
            'id' => $membre->id,
            'nom' => $membre->nom_complet ?? $membre->prenom . ' ' . $membre->nom,
            'type' => 'membre',
            'est_cjes' => false,
            'profil_id' => null,
            'cotisation_a_jour' => false,
        ];

        return $options;
    }

    /**
     * Vérifier si une entreprise est à jour dans ses cotisations.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController
     */
    public function verifierCotisationsEntreprise($entrepriseId): bool
    {
        $entreprise = Entreprise::find($entrepriseId);

        if (!$entreprise || !$entreprise->est_membre_cijes) {
            return false;
        }

        return Cotisation::where('entreprise_id', $entrepriseId)
            ->where('statut', 'paye')
            ->where('est_a_jour', 1)
            ->where('date_fin', '>=', now())
            ->exists();
    }

    /**
     * Récupérer les réductions applicables selon le contexte de paiement.
     *
     * @param array $contexte  Payment context array
     * @param int   $offretypeId  1=prestations, 2=formations, 3=événements, 4=espaces
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController
     */
    public function getReductionsPourContexte(array $contexte, int $offretypeId)
    {
        $reductions = collect();

        if ($contexte['type'] === 'entreprise' || $contexte['type'] === 'accompagnement_entreprise') {
            if ($contexte['est_cjes'] && $contexte['cotisation_a_jour'] && $contexte['profil_id']) {
                $reductions = Reductiontype::where('etat', 1)
                    ->where('offretype_id', $offretypeId)
                    ->where('entrepriseprofil_id', $contexte['profil_id'])
                    ->where(function ($query) {
                        $query->whereNull('date_debut')
                              ->orWhere(function ($subQuery) {
                                  $subQuery->where('date_debut', '<=', now())
                                           ->where('date_fin', '>=', now());
                              });
                    })
                    ->get();
            }
        }

        $reductionsGeneriques = Reductiontype::where('etat', 1)
            ->where('offretype_id', $offretypeId)
            ->where('entrepriseprofil_id', 0)
            ->where(function ($query) {
                $query->whereNull('date_debut')
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('date_debut', '<=', now())
                                   ->where('date_fin', '>=', now());
                      });
            })
            ->get();

        return $reductions->merge($reductionsGeneriques);
    }

    /**
     * Calculer le meilleur montant après application des réductions.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController
     */
    public function calculerMontantAvecReduction(float $montantOriginal, $reductions): array
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
            'meilleure_reduction' => $meilleureReduction,
            'economie' => $montantOriginal - $meilleurMontant,
        ];
    }

    /**
     * Résoudre le contexte de paiement à partir du type et de l'ID.
     *
     * Duplicated switch logic in: EspaceController::reserverStore,
     * PrestationController::inscrireStore, EvenementController::inscrireStore
     */
    public function resolveContextePaiement(string $contexteType, $contexteId, array $optionsPaiement): ?array
    {
        switch ($contexteType) {
            case 'entreprise':
                return collect($optionsPaiement['entreprises'] ?? [])->firstWhere('id', $contexteId);
            case 'accompagnement':
                return collect($optionsPaiement['accompagnements'] ?? [])->firstWhere('id', $contexteId);
            case 'membre':
                return $optionsPaiement['membre'];
            default:
                return null;
        }
    }

    /**
     * Sélection automatique du contexte de paiement lorsqu'aucun choix explicite.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController
     */
    public function autoSelectContextePaiement(array $optionsPaiement): array
    {
        if (count($optionsPaiement['accompagnements'] ?? []) === 1) {
            return $optionsPaiement['accompagnements'][0];
        }

        if (count($optionsPaiement['entreprises'] ?? []) === 1 && empty($optionsPaiement['accompagnements'])) {
            return $optionsPaiement['entreprises'][0];
        }

        return $optionsPaiement['membre'];
    }

    /**
     * Valider et récupérer le compte ressource pour le paiement.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController, FormationController
     */
    public function validateAndGetRessourceCompte(int $ressourcecompteId, $membre, array $entrepriseIds): Ressourcecompte
    {
        return Ressourcecompte::where('id', $ressourcecompteId)
            ->where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id)
                    ->orWhereIn('entreprise_id', $entrepriseIds);
            })
            ->firstOrFail();
    }

    /**
     * Vérifier la compatibilité du type de ressource avec le type d'offre.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController, FormationController
     */
    public function checkRessourceCompatibility(Ressourcecompte $ressourcecompte, int $offretypeId): bool
    {
        return Ressourcetypeoffretype::where('ressourcetype_id', $ressourcecompte->ressourcetype_id)
            ->where('offretype_id', $offretypeId)
            ->exists();
    }

    /**
     * Créer une transaction de débit sur un compte ressource.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController, FormationController
     */
    public function createDebitTransaction(Ressourcecompte $ressourcecompte, float $montant, string $reference): void
    {
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
    }

    /**
     * Créer une transaction de crédit sur un compte ressource.
     *
     * Duplicated in: PrestationController, FormationController
     */
    public function createCreditTransaction(Ressourcecompte $receveurCompte, float $montant, string $reference): void
    {
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

    /**
     * Générer une référence de paiement unique.
     *
     * @param string $prefix  e.g. 'PAI-ESP', 'PAI-PREST', 'PAI-EVT', 'PAI-FORM'
     */
    public function generateReference(string $prefix): string
    {
        return $prefix . '-' . strtoupper(Str::random(8));
    }

    /**
     * Déterminer les IDs d'accompagnement et d'entreprise à partir du contexte de paiement.
     *
     * Duplicated in: EspaceController, PrestationController, EvenementController
     */
    public function resolveContexteIds(array $contextePaiement): array
    {
        $accompagnementId = null;
        $entrepriseId = null;

        if ($contextePaiement['type'] === 'accompagnement_entreprise' || $contextePaiement['type'] === 'accompagnement_membre') {
            $accompagnementId = $contextePaiement['id'];
            $entrepriseId = $contextePaiement['entreprise_id'] ?? null;
        } elseif ($contextePaiement['type'] === 'entreprise') {
            $entrepriseId = $contextePaiement['id'];
        }

        return [
            'accompagnement_id' => $accompagnementId,
            'entreprise_id' => $entrepriseId,
        ];
    }
}
