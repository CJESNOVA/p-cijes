<?php

namespace App\Http\Controllers;

use App\Services\DiagnosticStatutService;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntrepriseProfilController extends Controller
{
    protected $diagnosticStatutService;

    public function __construct(DiagnosticStatutService $diagnosticStatutService)
    {
        $this->diagnosticStatutService = $diagnosticStatutService;
    }

    /**
     * Évaluer le profil d'une entreprise
     */
    public function evaluerProfil($entrepriseId)
    {
        $entreprise = Entreprise::findOrFail($entrepriseId);
        
        // Vérifier les permissions
        $this->autoriserEvaluation($entreprise);
        
        $resultat = $this->diagnosticStatutService->evaluerProfilEntreprise($entrepriseId);
        
        return response()->json([
            'success' => true,
            'data' => $resultat
        ]);
    }

    /**
     * Forcer la réévaluation du profil
     */
    public function forcerEvaluation($entrepriseId)
    {
        $entreprise = Entreprise::findOrFail($entrepriseId);
        
        // Vérifier les permissions (admin uniquement)
        $this->autoriserForcage($entreprise);
        
        $resultat = $this->diagnosticStatutService->evaluerProfilEntreprise($entrepriseId, true);
        
        return response()->json([
            'success' => true,
            'data' => $resultat,
            'message' => 'Évaluation forcée effectuée avec succès'
        ]);
    }

    /**
     * Obtenir l'historique des profils d'une entreprise
     */
    public function getHistorique($entrepriseId, $limit = 10)
    {
        $entreprise = Entreprise::findOrFail($entrepriseId);
        
        // Vérifier les permissions
        $this->autoriserConsultation($entreprise);
        
        // Utiliser les nouvelles évolutions au lieu de l'historique supprimé
        $evolutions = $this->diagnosticStatutService->getEvolutions($entrepriseId, $limit);
        
        return response()->json([
            'success' => true,
            'data' => $evolutions
        ]);
    }

    /**
     * Obtenir les statistiques des profils
     */
    public function getStatistiques()
    {
        // Réservé aux administrateurs
        $this->authorize('view-statistiques');
        
        $statistiques = $this->diagnosticStatutService->getStatistiquesProfils();
        
        return response()->json([
            'success' => true,
            'data' => $statistiques
        ]);
    }

    /**
     * Réévaluer tous les profils (admin uniquement)
     */
    public function reevaluerTous()
    {
        // Réservé aux administrateurs
        $this->authorize('reevaluer-profils');
        
        $resultats = $this->diagnosticStatutService->reevaluerTousLesProfils();
        
        return response()->json([
            'success' => true,
            'data' => $resultats,
            'message' => 'Réévaluation de tous les profils terminée'
        ]);
    }

    /**
     * Afficher le tableau de bord des profils
     */
    public function dashboard()
    {
        // Réservé aux administrateurs
        $this->authorize('view-dashboard-profils');
        
        $statistiques = $this->diagnosticStatutService->getStatistiquesProfils();
        
        return view('entrepriseprofil.dashboard', compact('statistiques'));
    }

    /**
     * Afficher les détails d'une entreprise
     */
    public function show($entrepriseId)
    {
        $entreprise = Entreprise::with(['diagnostics' => function($query) {
            $query->where('diagnosticstatut_id', 2)->latest();
        }])->findOrFail($entrepriseId);
        
        // Vérifier les permissions
        $this->autoriserConsultation($entreprise);
        
        // Utiliser les nouvelles évolutions au lieu de l'historique supprimé
        $evolutions = $this->diagnosticStatutService->getEvolutions($entrepriseId, 20);
        
        return view('entrepriseprofil.show', compact('entreprise', 'evolutions'));
    }

    /**
     * Vérifier si l'utilisateur peut évaluer cette entreprise
     */
    private function autoriserEvaluation($entreprise)
    {
        $userId = Auth::id();
        
        // L'utilisateur doit être membre de l'entreprise ou admin
        $estMembre = $entreprise->entreprisemembres()->where('user_id', $userId)->exists();
        $estAdmin = Auth::user()->hasRole('admin');
        
        if (!$estMembre && !$estAdmin) {
            abort(403, 'Vous n\'êtes pas autorisé à évaluer cette entreprise.');
        }
    }

    /**
     * Vérifier si l'utilisateur peut forcer l'évaluation
     */
    private function autoriserForcage($entreprise)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Seuls les administrateurs peuvent forcer une évaluation.');
        }
    }

    /**
     * Vérifier si l'utilisateur peut consulter cette entreprise
     */
    private function autoriserConsultation($entreprise)
    {
        $userId = Auth::id();
        
        // L'utilisateur doit être membre de l'entreprise ou admin
        $estMembre = $entreprise->entreprisemembres()->where('user_id', $userId)->exists();
        $estAdmin = Auth::user()->hasRole('admin');
        
        if (!$estMembre && !$estAdmin) {
            abort(403, 'Vous n\'êtes pas autorisé à consulter cette entreprise.');
        }
    }
}
