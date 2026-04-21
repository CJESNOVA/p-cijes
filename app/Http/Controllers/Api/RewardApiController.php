<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecompenseService;
use App\Models\Membre;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RewardApiController extends Controller
{
    protected $recompenseService;

    public function __construct(RecompenseService $recompenseService)
    {
        $this->recompenseService = $recompenseService;
    }

    /**
     * Attribuer une récompense via API
     * 
     * @bodyParam action_code string required Code de l'action (ex: DEMANDE_SIKA, RECEPTION_SIKA)
     * @bodyParam user_id integer required ID de l'utilisateur Laravel
     * @bodyParam supabase_startup_id string|null ID Supabase de l'entreprise
     * @bodyParam montant numeric|null Montant pour calcul de pourcentage
     * @bodyParam reference string|null Référence personnalisée de la transaction
     * @bodyParam description string|null Description de la transaction
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "Récompense attribuée avec succès",
     *   "data": {
     *     "recompense_id": 123,
     *     "recompense_valeur": 500,
     *     "transaction_id": 456,
     *     "solde_mis_a_jour": true
     *   }
     * }
     */
    public function attribuerRecompense(Request $request)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'action_code' => 'required|string|max:50',
            'user_id' => 'required_without:supabase_user_id|string|max:255',
            'supabase_user_id' => 'required_without:user_id|string|max:255',
            'supabase_startup_id' => 'nullable|string|max:255',
            'montant' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 1. Récupérer l'utilisateur Laravel (soit via supabase_user_id, soit via user_id direct)
            $user = null;
            
            if ($request->filled('supabase_user_id')) {
                // Cas 1: Recherche via supabase_user_id
                $user = \App\Models\User::where('supabase_user_id', $request->supabase_user_id)->first();
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non trouvé pour ce supabase_user_id'
                    ], 404);
                }
            } elseif ($request->filled('user_id')) {
                // Cas 2: Recherche directe via user_id (pour compatibilité)
                $user = \App\Models\User::find($request->user_id);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non trouvé pour ce user_id'
                    ], 404);
                }
            }

            // 2. Récupérer le membre à partir du user_id
            $membre = Membre::where('user_id', $user->id)->first();
            
            if (!$membre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membre non trouvé pour cet utilisateur'
                ], 404);
            }

            // 3. Récupérer l'entreprise si supabase_startup_id est fourni
            $entreprise = null;
            if ($request->supabase_startup_id) {
                $entreprise = Entreprise::where('supabase_startup_id', $request->supabase_startup_id)->first();
                
                if (!$entreprise) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Entreprise non trouvée pour ce supabase_startup_id'
                    ], 404);
                }
            }

            // 3. Attribuer la récompense
            $recompense = $this->recompenseService->attribuerRecompense(
                $request->action_code,
                $membre,
                $entreprise,
                null, // Pas de source_id pour les appels API
                $request->montant
            );

            if (!$recompense) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de l\'attribution de récompense'
                ], 500);
            }

            // 4. Journaliser l'appel API
            Log::info('Récompense attribuée via API', [
                'action_code' => $request->action_code,
                'user_id' => $request->user_id ?? $request->supabase_user_id,
                'membre_id' => $membre->id,
                'entreprise_id' => $entreprise?->id,
                'montant' => $request->montant,
                'recompense_id' => $recompense->id,
                'recompense_valeur' => $recompense->valeur,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Récompense attribuée avec succès',
                'data' => [
                    'recompense_id' => $recompense->id,
                    'recompense_valeur' => $recompense->valeur,
                    'recompense_points' => $recompense->valeur,
                    'membre_info' => [
                        'id' => $membre->id,
                        'nom' => $membre->nom,
                        'prenom' => $membre->prenom,
                        'email' => $membre->email,
                    ],
                    'entreprise_info' => $entreprise ? [
                        'id' => $entreprise->id,
                        'nom' => $entreprise->nom,
                        'supabase_startup_id' => $entreprise->supabase_startup_id,
                    ] : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'attribution de récompense via API', [
                'action_code' => $request->action_code,
                'user_id' => $request->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Lister les actions de récompense disponibles
     */
    public function listerActions()
    {
        $actions = \DB::table('actions')
            ->select('code', 'titre', 'point', 'seuil', 'description')
            ->orderBy('code')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des actions de récompense disponibles',
            'data' => $actions
        ]);
    }

    /**
     * Vérifier les récompenses d'un membre
     */
    public function verifierRecompenses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required_without:supabase_user_id|string|max:255',
            'supabase_user_id' => 'required_without:user_id|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // 1. Récupérer l'utilisateur Laravel (soit via supabase_user_id, soit via user_id direct)
        $user = null;
        
        if ($request->filled('supabase_user_id')) {
            // Cas 1: Recherche via supabase_user_id
            $user = \App\Models\User::where('supabase_user_id', $request->supabase_user_id)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé pour ce supabase_user_id'
                ], 404);
            }
        } elseif ($request->filled('user_id')) {
            // Cas 2: Recherche directe via user_id (pour compatibilité)
            $user = \App\Models\User::find($request->user_id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé pour ce user_id'
                ], 404);
            }
        }

        // 2. Récupérer le membre à partir du user_id
        $membre = Membre::where('user_id', $user->id)->first();
        
        if (!$membre) {
            return response()->json([
                'success' => false,
                'message' => 'Membre non trouvé pour cet utilisateur'
            ], 404);
        }

        $recompenses = \DB::table('recompenses')
            ->join('actions', 'recompenses.action_id', '=', 'actions.id')
            ->where('recompenses.membre_id', $membre->id)
            ->select('recompenses.*', 'actions.code as action_code', 'actions.titre as action_titre')
            ->orderBy('recompenses.created_at', 'desc')
            ->limit(50)
            ->get();

        // Calculer le total des points
        $totalPoints = $recompenses->sum('valeur');

        return response()->json([
            'success' => true,
            'message' => 'Récompenses du membre',
            'data' => [
                'membre' => [
                    'id' => $membre->id,
                    'nom' => $membre->nom,
                    'prenom' => $membre->prenom,
                    'email' => $membre->email,
                    'supabase_user_id' => $user->supabase_user_id,
                ],
                'total_points' => $totalPoints,
                'nombre_recompenses' => $recompenses->count(),
                'recompenses' => $recompenses,
            ]
        ]);
    }
}
