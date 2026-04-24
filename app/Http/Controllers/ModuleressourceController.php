<?php

namespace App\Http\Controllers;

use App\Models\Moduleressource;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Http\Controllers\ActionPaiementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ModuleressourceController extends Controller
{
    /**
     * Lister les modules ressources
     */
    public function index(Request $request)
    {
        $query = Moduleressource::with(['membre', 'entreprise', 'paiementstatut']);

        if ($request->filled('membre_id')) {
            $query->where('membre_id', $request->membre_id);
        }

        if ($request->filled('entreprise_id')) {
            $query->where('entreprise_id', $request->entreprise_id);
        }

        if ($request->filled('module_type')) {
            $query->where('module_type', $request->module_type);
        }

        $modules = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $modules->items(),
            'pagination' => [
                'current_page' => $modules->currentPage(),
                'per_page' => $modules->perPage(),
                'total' => $modules->total(),
                'last_page' => $modules->lastPage(),
            ]
        ]);
    }

    /**
     * Afficher un module ressource spécifique
     */
    public function show($id)
    {
        $moduleRessource = Moduleressource::with(['membre', 'entreprise', 'paiementstatut'])
            ->find($id);

        if (!$moduleRessource) {
            return response()->json([
                'success' => false,
                'message' => 'Module ressource non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Module ressource récupéré avec succès',
            'data' => [
                'id' => $moduleRessource->id,
                'module_type' => $moduleRessource->module_type,
                'module_id' => $moduleRessource->module_id,
                'montant' => $moduleRessource->montant,
                'reference' => $moduleRessource->reference,
                'description' => $moduleRessource->description,
                'module_titre' => $moduleRessource->module_titre,
                'etat' => $moduleRessource->etat,
                'spotlight' => $moduleRessource->spotlight,
                'paiement_statut' => $moduleRessource->paiementstatut->libelle ?? 'Non défini',
                'membre' => $moduleRessource->membre ? [
                    'id' => $moduleRessource->membre->id,
                    'nom' => $moduleRessource->membre->nom,
                    'prenom' => $moduleRessource->membre->prenom,
                    'email' => $moduleRessource->membre->email,
                ] : null,
                'entreprise' => $moduleRessource->entreprise ? [
                    'id' => $moduleRessource->entreprise->id,
                    'nom' => $moduleRessource->entreprise->nom,
                    'supabase_startup_id' => $moduleRessource->entreprise->supabase_startup_id,
                ] : null,
                'created_at' => $moduleRessource->created_at,
                'updated_at' => $moduleRessource->updated_at,
            ]
        ]);
    }

    /**
     * Attribuer un module ressource en interne (version simplifiée)
     * 
     * @param string $moduleType
     * @param int $moduleId
     * @param string $actionCode
     * @param Membre $membre
     * @param array $options
     * @return array
     */
    public function attribuerModuleViaAction(string $moduleType, int $moduleId, string $actionCode, Membre $membre, array $options = [])
    {
        try {
            DB::beginTransaction();

            // 1. Récupérer le montant depuis l'action
            $actionController = new ActionPaiementController();
            $actionData = $actionController->getActionPourPaiement($actionCode);
            
            if (!$actionData) {
                return [
                    'success' => false,
                    'message' => "Action '{$actionCode}' non trouvée"
                ];
            }

            $montant = $actionData['montant_retrait'];
            $reference = $options['reference'] ?? 'ACT-' . date('YmdHis') . '-' . Str::random(6);

            // 2. Si montant > 0, trouver le compte et retirer
            $ressourcecompte = null;
            if ($montant > 0) {
                // Récupérer les entreprises du membre (peut être vide)
                $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
                    ->pluck('entreprise_id')
                    ->toArray();

                // Utiliser l'action pour trouver le bon compte selon la stratégie
                $ressourcecompte = $actionController->trouverComptePourAction($actionCode, $membre, $entrepriseIds, $montant);

                if (!$ressourcecompte) {
                    return [
                        'success' => false,
                        'message' => "Aucun compte disponible pour cette action. Types recherchés: " . 
                                   (empty($entrepriseIds) ? "comptes membre uniquement" : "comptes membre et entreprises")
                    ];
                }

                // 3. Retrait simple
                $soldeAvant = $ressourcecompte->solde;
                
                Ressourcetransaction::create([
                    'montant' => -$montant,
                    'reference' => $reference,
                    'ressourcecompte_id' => $ressourcecompte->id,
                    'datetransaction' => now(),
                    'operationtype_id' => 2, // Débit
                    'spotlight' => 0,
                    'etat' => 1,
                ]);
                
                $ressourcecompte->decrement('solde', $montant);
                
                // Vérifier que le solde a bien été mis à jour
                $ressourcecompte->refresh(); // Recharger depuis la base
                $soldeApres = $ressourcecompte->solde;
                
                Log::info('Retrait effectué avec succès', [
                    'reference' => $reference,
                    'montant_retrait' => $montant,
                    'compte_id' => $ressourcecompte->id,
                    'solde_avant' => $soldeAvant,
                    'solde_apres' => $soldeApres,
                    'difference' => $soldeAvant - $soldeApres,
                    'verification' => ($soldeAvant - $soldeApres) == $montant ? 'OK' : 'ERREUR'
                ]);
            }

            // 4. Créer la trace dans module ressource
            $moduleRessource = Moduleressource::create([
                'montant' => $montant,
                'reference' => $reference,
                'description' => $options['description'] ?? "Module via action {$actionCode}",
                'module_type' => $moduleType,
                'module_id' => $moduleId,
                'membre_id' => $membre->id,
                'entreprise_id' => $options['entreprise']?->id,
                'ressourcecompte_id' => $ressourcecompte?->id,
                'paiementstatut_id' => 1,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            Log::info('Module attribué via action', [
                'action_code' => $actionCode,
                'module_id' => $moduleRessource->id,
                'montant_retrait' => $montant,
                'compte_id' => $ressourcecompte?->id,
                'compte_type' => $ressourcecompte ? ($ressourcecompte->entreprise_id ? 'entreprise' : 'membre') : null,
                'membre_id' => $membre->id,
                'entreprise_id' => $options['entreprise']?->id,
                'contexte' => empty($entrepriseIds) ? 'membre_seul' : 'membre_entreprise'
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Module attribué avec succès',
                'data' => [
                    'module_ressource_id' => $moduleRessource->id,
                    'action_code' => $actionCode,
                    'montant' => $montant,
                    'reference' => $reference,
                    'contexte' => [
                        'type' => empty($entrepriseIds) ? 'membre_seul' : 'membre_entreprise',
                        'compte_utilise' => $ressourcecompte ? [
                            'id' => $ressourcecompte->id,
                            'type' => $ressourcecompte->entreprise_id ? 'entreprise' : 'membre',
                            'solde_avant' => $soldeAvant ?? null,
                            'solde_apres' => $soldeApres ?? null,
                            'montant_retrait' => $montant,
                            'verification' => isset($soldeAvant, $soldeApres) ? (($soldeAvant - $soldeApres) == $montant ? 'OK' : 'ERREUR') : null
                        ] : null
                    ]
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur attribution via action', [
                'action_code' => $actionCode,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'attribution'
            ];
        }
    }
}
