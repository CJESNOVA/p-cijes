<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Membre;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Log;

class ActionPaiementController extends Controller
{
    /**
     * Récupérer une action spécifique pour le paiement
     * 
     * @param string $actionCode
     * @return array|null
     */
    public function getActionPourPaiement(string $actionCode)
    {
        try {
            // Récupérer l'action
            $action = Action::where('code', $actionCode)->first();
            
            if (!$action) {
                Log::warning('Action non trouvée pour paiement', [
                    'action_code' => $actionCode
                ]);
                return null;
            }

            // Analyser les caractéristiques de l'action pour le paiement
            $caracteristiques = $this->analyserCaracteristiquesAction($action);
            
            // Déterminer la stratégie de ressource
            $strategieRessource = $this->determinerStrategieRessource($action);
            
            Log::info('Action récupérée pour paiement', [
                'action_code' => $actionCode,
                'action_id' => $action->id,
                'titre' => $action->titre,
                'caracteristiques' => $caracteristiques,
                'strategie_ressource' => $strategieRessource
            ]);

            return [
                'action' => $action,
                'caracteristiques' => $caracteristiques,
                'strategie_ressource' => $strategieRessource,
                'montant_retrait' => $this->calculerMontantRetrait($action, $caracteristiques)
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'action pour paiement', [
                'action_code' => $actionCode,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Déterminer la stratégie de ressource pour le paiement
     * 
     * @param Action $action
     * @return array
     */
    private function determinerStrategieRessource(Action $action)
    {
        $strategie = [
            'type' => 'automatique', // 'specifique' ou 'automatique'
            'ressourcetype_id' => null,
            'description' => ''
        ];

        // Vérifier si une ressource spécifique est définie
        if ($action->ressourcetype_id && $action->ressourcetype_id > 0) {
            $strategie['type'] = 'specifique';
            $strategie['ressourcetype_id'] = $action->ressourcetype_id;
            $strategie['description'] = "Utilisation uniquement des comptes de ressource type {$action->ressourcetype_id}";
        } else {
            $strategie['type'] = 'automatique';
            $strategie['description'] = "Recherche automatique par ordre de priorité 2-3-4-1";
        }

        Log::info('Stratégie de ressource déterminée', [
            'action_code' => $action->code,
            'ressourcetype_id_action' => $action->ressourcetype_id,
            'strategie_type' => $strategie['type'],
            'description' => $strategie['description']
        ]);

        return $strategie;
    }

    /**
     * Analyser les caractéristiques de l'action pour comprendre son fonctionnement
     * 
     * @param Action $action
     * @return array
     */
    private function analyserCaracteristiquesAction(Action $action)
    {
        $caracteristiques = [
            'type_calcul' => 'fixe', // fixe ou pourcentage
            'valeur_base' => null,
            'seuil_pourcentage' => null,
            'ressource_type_id' => $action->ressourcetype_id,
            'a_limite' => !empty($action->limite),
            'valeur_limite' => $action->limite,
            'est_actif' => $action->etat == 1,
        ];

        // Déterminer le type de calcul
        if ($action->seuil && str_contains($action->seuil, '%')) {
            $caracteristiques['type_calcul'] = 'pourcentage';
            $caracteristiques['seuil_pourcentage'] = (float) str_replace('%', '', $action->seuil);
        } else {
            $caracteristiques['type_calcul'] = 'fixe';
            $caracteristiques['valeur_base'] = $action->point;
        }

        return $caracteristiques;
    }

    /**
     * Calculer le montant qui sera retiré pour le paiement
     * 
     * @param Action $action
     * @param array $caracteristiques
     * @param float|null $montantReference
     * @return float
     */
    public function calculerMontantRetrait(Action $action, array $caracteristiques, ?float $montantReference = null)
    {
        $montantRetrait = 0;

        switch ($caracteristiques['type_calcul']) {
            case 'pourcentage':
                if ($montantReference !== null) {
                    $pourcentage = $caracteristiques['seuil_pourcentage'];
                    $montantRetrait = $montantReference * ($pourcentage / 100);
                }
                break;

            case 'fixe':
                $montantRetrait = $caracteristiques['valeur_base'] ?? 0;
                break;

            default:
                $montantRetrait = 0;
                break;
        }

        Log::info('Calcul montant retrait pour action', [
            'action_code' => $action->code,
            'type_calcul' => $caracteristiques['type_calcul'],
            'montant_reference' => $montantReference,
            'montant_retrait' => $montantRetrait,
            'details' => $caracteristiques
        ]);

        return (float) $montantRetrait;
    }

    /**
     * Vérifier si une action peut être appliquée à un membre/entreprise
     * 
     * @param string $actionCode
     * @param Membre $membre
     * @param Entreprise|null $entreprise
     * @return array
     */
    public function verifierApplicabiliteAction(string $actionCode, Membre $membre, ?Entreprise $entreprise = null)
    {
        $actionData = $this->getActionPourPaiement($actionCode);
        
        if (!$actionData) {
            return [
                'applicable' => false,
                'raison' => 'Action non trouvée'
            ];
        }

        $action = $actionData['action'];
        $caracteristiques = $actionData['caracteristiques'];

        // Vérifier si l'action est active
        if (!$caracteristiques['est_actif']) {
            return [
                'applicable' => false,
                'raison' => 'Action inactive'
            ];
        }

        // Vérifier les limites si existantes
        if ($caracteristiques['a_limite']) {
            // Ici vous pourriez ajouter la logique pour vérifier
            // si le membre/entreprise a déjà atteint la limite
            // Pour l'instant, on considère que c'est applicable
        }

        return [
            'applicable' => true,
            'action' => $action,
            'caracteristiques' => $caracteristiques,
            'montant_retrait_estime' => $actionData['montant_retrait']
        ];
    }

    /**
     * Trouver le compte ressource selon la stratégie de l'action
     * 
     * @param string $actionCode
     * @param Membre $membre
     * @param array $entrepriseIds
     * @param float $montantRequis
     * @return \App\Models\Ressourcecompte|null
     */
    public function trouverComptePourAction(string $actionCode, Membre $membre, array $entrepriseIds, float $montantRequis)
    {
        $actionData = $this->getActionPourPaiement($actionCode);
        
        if (!$actionData) {
            Log::warning('Action non trouvée pour recherche de compte', [
                'action_code' => $actionCode
            ]);
            return null;
        }

        $strategie = $actionData['strategie_ressource'];
        
        switch ($strategie['type']) {
            case 'specifique':
                return $this->trouverCompteSpecifique($membre, $entrepriseIds, $montantRequis, $strategie['ressourcetype_id']);
                
            case 'automatique':
                return $this->trouverCompteParPriorite($membre, $entrepriseIds, $montantRequis, $actionData);
                
            default:
                Log::error('Stratégie de ressource inconnue', [
                    'action_code' => $actionCode,
                    'strategie_type' => $strategie['type']
                ]);
                return null;
        }
    }

    /**
     * Trouver un compte ressource spécifique
     * 
     * @param Membre $membre
     * @param array $entrepriseIds
     * @param float $montantRequis
     * @param int $ressourcetypeId
     * @return \App\Models\Ressourcecompte|null
     */
    private function trouverCompteSpecifique($membre, $entrepriseIds, $montantRequis, $ressourcetypeId)
    {
        $compte = \App\Models\Ressourcecompte::where('ressourcetype_id', $ressourcetypeId)
            ->where(function ($q) use ($membre, $entrepriseIds) {
                $q->where('membre_id', $membre->id);
                if (!empty($entrepriseIds)) {
                    $q->orWhereIn('entreprise_id', $entrepriseIds);
                }
            })
            ->where('etat', 1)
            ->where('solde', '>=', $montantRequis)
            ->first();

        if ($compte) {
            Log::info('Compte spécifique trouvé', [
                'ressourcetype_id' => $ressourcetypeId,
                'compte_id' => $compte->id,
                'solde' => $compte->solde,
                'montant_requis' => $montantRequis
            ]);
        } else {
            Log::info('Aucun compte spécifique disponible', [
                'ressourcetype_id' => $ressourcetypeId,
                'membre_id' => $membre->id,
                'entreprise_ids' => $entrepriseIds,
                'montant_requis' => $montantRequis
            ]);
        }

        return $compte;
    }

    /**
     * Trouver un compte ressource par ordre de priorité 2-3-4-1
     * 
     * @param Membre $membre
     * @param array $entrepriseIds
     * @param float $montantRequis
     * @param array $actionData
     * @return \App\Models\Ressourcecompte|null
     */
    public function trouverCompteParPriorite($membre, $entrepriseIds, $montantRequis, $actionData)
    {
        // Ordre de priorité : 2-3-4-1
        $priorites = [2, 3, 4, 1];

        foreach ($priorites as $typeId) {
            $compte = \App\Models\Ressourcecompte::where('ressourcetype_id', $typeId)
                ->where(function ($q) use ($membre, $entrepriseIds) {
                    $q->where('membre_id', $membre->id);
                    if (!empty($entrepriseIds)) {
                        $q->orWhereIn('entreprise_id', $entrepriseIds);
                    }
                })
                ->where('etat', 1)
                ->where('solde', '>=', $montantRequis)
                ->first();

            if ($compte) {
                // Si ressource_type_id = 0, ignorer la compatibilité et utiliser tous les types
                if ($actionData['caracteristiques']['ressource_type_id'] == 0) {
                    Log::info('Compte trouvé par priorité (compatibilité ignorée)', [
                        'type_id' => $typeId,
                        'compte_id' => $compte->id,
                        'solde' => $compte->solde,
                        'montant_requis' => $montantRequis,
                        'ressource_type_action' => $actionData['caracteristiques']['ressource_type_id'],
                        'compatibility_ignored' => true
                    ]);
                    return $compte;
                }
                
                // Si ressource_type_id spécifique, vérifier que le type correspond
                if ($actionData['caracteristiques']['ressource_type_id'] != 0) {
                    $typeRequis = $actionData['caracteristiques']['ressource_type_id'];
                    if ($typeId == $typeRequis) {
                        Log::info('Compte trouvé par type spécifique', [
                            'type_id' => $typeId,
                            'compte_id' => $compte->id,
                            'solde' => $compte->solde,
                            'montant_requis' => $montantRequis,
                            'type_requis' => $typeRequis,
                            'correspondance' => 'OK'
                        ]);
                        return $compte;
                    } else {
                        // Ce n'est pas le bon type, continuer la recherche
                        continue;
                    }
                }
                
                // Sinon, vérifier la compatibilité avec les modules ressources
                $isCompatible = \App\Models\Ressourcetypeoffretype::where('ressourcetype_id', $typeId)
                    ->where('offretype_id', 3) // 3 = modules ressources
                    ->exists();

                if ($isCompatible) {
                    Log::info('Compte trouvé par priorité', [
                        'type_id' => $typeId,
                        'compte_id' => $compte->id,
                        'solde' => $compte->solde,
                        'montant_requis' => $montantRequis,
                        'compatible' => true
                    ]);
                    return $compte;
                }
            }
        }

        Log::info('Aucun compte disponible par priorité', [
            'membre_id' => $membre->id,
            'entreprise_ids' => $entrepriseIds,
            'montant_requis' => $montantRequis,
            'priorites_recherchees' => $priorites
        ]);

        return null;
    }

    /**
     * Simuler le paiement pour une action donnée
     * 
     * @param string $actionCode
     * @param Membre $membre
     * @param Entreprise|null $entreprise
     * @param float|null $montantReference
     * @return array
     */
    public function simulerPaiementAction(string $actionCode, Membre $membre, ?Entreprise $entreprise = null, ?float $montantReference = null)
    {
        $verification = $this->verifierApplicabiliteAction($actionCode, $membre, $entreprise);
        
        if (!$verification['applicable']) {
            return [
                'success' => false,
                'message' => $verification['raison'],
                'simulation' => null
            ];
        }

        $action = $verification['action'];
        $caracteristiques = $verification['caracteristiques'];

        // Calculer le montant exact qui sera retiré
        $montantRetrait = $this->calculerMontantRetrait($action, $caracteristiques, $montantReference);

        return [
            'success' => true,
            'message' => 'Simulation réussie',
            'simulation' => [
                'action_code' => $actionCode,
                'action_titre' => $action->titre,
                'type_calcul' => $caracteristiques['type_calcul'],
                'montant_reference' => $montantReference,
                'montant_retrait' => $montantRetrait,
                'ressource_type_id' => $caracteristiques['ressource_type_id'],
                'membre_id' => $membre->id,
                'entreprise_id' => $entreprise?->id,
                'details_calcul' => $this->getDetailsCalcul($action, $caracteristiques, $montantReference, $montantRetrait)
            ]
        ];
    }

    /**
     * Obtenir les détails du calcul pour affichage
     * 
     * @param Action $action
     * @param array $caracteristiques
     * @param float|null $montantReference
     * @param float $montantRetrait
     * @return array
     */
    private function getDetailsCalcul(Action $action, array $caracteristiques, ?float $montantReference, float $montantRetrait)
    {
        $details = [
            'formule' => '',
            'explication' => ''
        ];

        switch ($caracteristiques['type_calcul']) {
            case 'pourcentage':
                $pourcentage = $caracteristiques['seuil_pourcentage'];
                $details['formule'] = "{$montantReference} FCFA × {$pourcentage}% = {$montantRetrait} FCFA";
                $details['explication'] = "Le montant retourné correspond à {$pourcentage}% du montant de référence ({$montantReference} FCFA)";
                break;

            case 'fixe':
                $valeur = $caracteristiques['valeur_base'];
                $details['formule'] = "{$valeur} FCFA (montant fixe)";
                $details['explication'] = "Le montant retourné est fixé à {$valeur} FCFA";
                break;
        }

        return $details;
    }
}
