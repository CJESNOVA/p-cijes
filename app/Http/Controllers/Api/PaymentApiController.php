<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuleressourceController;
use App\Models\Membre;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentApiController extends Controller
{
    protected $moduleController;

    public function __construct(ModuleressourceController $moduleController)
    {
        $this->moduleController = $moduleController;
    }

    /**
     * Attribuer un paiement via action API
     * 
     * @bodyParam action_code string required Code de l'action (ex: PREMIER_DIAG_DIRIGEANT, AUTRE_DIAG_DIRIGEANT, PREMIER_DIAG_ENTREPRISE, AUTRE_DIAG_ENTREPRISE, TEST_CLASSIFICATION_V2)
     * @bodyParam user_id string required ID de l'utilisateur Laravel (alternative à supabase_user_id)
     * @bodyParam supabase_user_id string required ID de l'utilisateur Supabase (alternative à user_id)
     * @bodyParam supabase_startup_id string optional ID de la startup Supabase
     * @bodyParam entreprise_id int optional ID de l'entreprise
     * @bodyParam diagnostic_id int optional ID du diagnostic
     * @bodyParam montant numeric optional Montant personnalisé
     * @bodyParam reference string optional Référence personnalisée
     * @bodyParam description string optional Description personnalisée
     */
    public function triggerPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action_code' => 'required|string|max:50',
            'user_id' => 'required_without:supabase_user_id|string|max:255',
            'supabase_user_id' => 'required_without:user_id|string|max:255',
            'supabase_startup_id' => 'nullable|string|max:255',
            'entreprise_id' => 'nullable|integer|exists:entreprises,id',
            'diagnostic_id' => 'nullable|integer',
            'montant' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée pour paiement API', [
                'errors' => $validator->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
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
            } elseif ($request->entreprise_id) {
                $entreprise = Entreprise::find($request->entreprise_id);
            }

            // Préparer les données pour le paiement
            $paymentData = [
                'entreprise' => $entreprise,
                'description' => $request->description ?? 'Paiement via API - ' . $request->action_code,
                'reference' => $request->reference ?? 'API-' . $request->action_code . '-' . date('YmdHis')
            ];

            // Si diagnostic_id fourni, l'ajouter au contexte
            if ($request->diagnostic_id) {
                $paymentData['diagnostic_id'] = $request->diagnostic_id;
            }

            Log::info('Déclenchement paiement via API', [
                'action_code' => $request->action_code,
                'user_id' => $request->user_id,
                'supabase_user_id' => $request->supabase_user_id,
                'membre_id' => $membre->id,
                'entreprise_id' => $request->entreprise_id,
                'supabase_startup_id' => $request->supabase_startup_id,
                'diagnostic_id' => $request->diagnostic_id,
                'reference' => $paymentData['reference']
            ]);

            // Appeler le ModuleressourceController
            $resultat = $this->moduleController->attribuerModuleViaAction(
                'api', // Type de source API
                $request->diagnostic_id ?? 0,
                $request->action_code,
                $membre,
                $paymentData
            );

            if ($resultat['success']) {
                Log::info('Paiement API effectué avec succès', [
                    'action_code' => $request->action_code,
                    'user_id' => $request->user_id,
                    'supabase_user_id' => $request->supabase_user_id,
                    'membre_id' => $membre->id,
                    'module_ressource_id' => $resultat['data']['module_ressource_id'],
                    'montant' => $resultat['data']['montant'],
                    'reference' => $paymentData['reference']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement effectué avec succès',
                    'data' => [
                        'payment_id' => $resultat['data']['module_ressource_id'],
                        'montant' => $resultat['data']['montant'],
                        'reference' => $paymentData['reference'],
                        'action_code' => $request->action_code,
                        'user_id' => $request->user_id,
                        'supabase_user_id' => $request->supabase_user_id,
                        'membre_id' => $membre->id,
                        'entreprise_id' => $request->entreprise_id
                    ]
                ], 200);
            } else {
                Log::warning('Échec paiement API', [
                    'action_code' => $request->action_code,
                    'user_id' => $request->user_id,
                    'supabase_user_id' => $request->supabase_user_id,
                    'membre_id' => $membre->id,
                    'erreur' => $resultat['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Échec du paiement',
                    'error' => $resultat['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors du paiement API', [
                'action_code' => $request->action_code,
                'user_id' => $request->user_id,
                'supabase_user_id' => $request->supabase_user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister les actions de paiement disponibles
     */
    public function listPaymentActions()
    {
        $actions = \DB::table('actions')
            ->select('code', 'titre', 'point', 'seuil', 'description')
            ->orderBy('code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $actions
        ], 200);
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus($reference)
    {
        try {
            // Logique pour vérifier le statut basé sur la référence
            // Pour l'instant, retourner un statut générique
            
            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $reference,
                    'status' => 'completed', // pending, completed, failed
                    'message' => 'Paiement traité avec succès'
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur vérification statut paiement', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
