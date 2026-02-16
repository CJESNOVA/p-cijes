<?php

namespace App\Services;

use App\Models\Diagnostic;
use App\Models\Diagnosticstatut;
use App\Models\Diagnosticstatutregle;
use App\Models\Diagnosticorientation;
use App\Models\Diagnosticmodulescore;
use App\Models\Diagnosticblocstatut;
use App\Models\Diagnosticevolution;
use App\Models\Entreprise;
use App\Models\Diagnosticmodule;

class DiagnosticStatutService
{
    /**
     * Évaluer et mettre à jour le statut d'un diagnostic
     */
    public function evaluerStatutDiagnostic($diagnosticId, $force = false)
    {
        $diagnostic = Diagnostic::with(['diagnosticmodulescores', 'entreprise'])->findOrFail($diagnosticId);
        $ancienStatutId = $diagnostic->diagnosticstatut_id;

        // Calculer les scores par bloc
        $scoresParBloc = $this->calculerScoresParBloc($diagnostic);
        $scoreGlobal = $this->calculerScoreGlobal($scoresParBloc);

        // Trouver le statut approprié selon les règles
        $nouveauStatut = $this->trouverStatutSelonRegles($scoresParBloc, $scoreGlobal, $diagnostic);

        if ($nouveauStatut && ($force || $ancienStatutId !== $nouveauStatut->id)) {
            // Mettre à jour le diagnostic
            $diagnostic->update([
                'diagnosticstatut_id' => $nouveauStatut->id,
                'scoreglobal' => $scoreGlobal,
                'entrepriseprofil_id' => $diagnostic->entreprise->entrepriseprofil_id,
            ]);

            // Créer une évolution si l'entreprise est associée ou si c'est un diagnostic PME
            if ($diagnostic->entreprise_id || $diagnostic->entreprise_id == 0) {
                $entrepriseIdPourEvolution = $diagnostic->entreprise_id ?: 0;
                
                \Log::info('evaluerStatutDiagnostic - création évolution', [
                    'diagnostic_id' => $diagnostic->id,
                    'entreprise_id' => $entrepriseIdPourEvolution,
                    'diagnostic_entreprise_id' => $diagnostic->entreprise_id,
                    'raison' => 'Changement de statut automatique'
                ]);
                
                $derniereEvolution = Diagnosticevolution::where('entreprise_id', $entrepriseIdPourEvolution)
                    ->orderBy('created_at', 'desc')
                    ->first();

                Diagnosticevolution::creerEvolution(
                    $entrepriseIdPourEvolution,
                    $diagnostic->id,
                    $derniereEvolution ? $derniereEvolution->diagnostic_id : null,
                    'Changement de statut automatique'
                );
            } else {
                \Log::warning('evaluerStatutDiagnostic - pas de création évolution', [
                    'diagnostic_id' => $diagnostic->id,
                    'entreprise_id' => $diagnostic->entreprise_id,
                    'raison' => 'entreprise_id null et différent de 0'
                ]);
            }

            return [
                'statut_change' => true,
                'ancien_statut' => $ancienStatutId ? Diagnosticstatut::find($ancienStatutId) : null,
                'nouveau_statut' => $nouveauStatut,
                'score_global' => $scoreGlobal,
                'scores_par_bloc' => $scoresParBloc,
            ];
        }

        return [
            'statut_change' => false,
            'statut_actuel' => $diagnostic->diagnosticstatut,
            'score_global' => $scoreGlobal,
            'scores_par_bloc' => $scoresParBloc,
        ];
    }

    /**
     * Évaluer et mettre à jour le profil d'une entreprise (PÉPITE/ÉMERGENTE/ÉLITE)
     */
    public function evaluerProfilEntreprise($entrepriseId, $force = false, $diagnosticId = null)
    {
        \Log::info('evaluerProfilEntreprise - début', [
            'entrepriseId' => $entrepriseId,
            'diagnosticId' => $diagnosticId
        ]);
        
        $entreprise = Entreprise::findOrFail($entrepriseId);
        
        \Log::info('evaluerProfilEntreprise - entreprise trouvée', [
            'entreprise_id' => $entreprise->id,
            'entreprise_nom' => $entreprise->nom,
            'entrepriseprofil_id' => $entreprise->entrepriseprofil_id,
            'entrepriseprofil' => $entreprise->entrepriseprofil
        ]);
        
        // Si un diagnosticId est fourni, l'utiliser directement
        if ($diagnosticId) {
            \Log::info('evaluerProfilEntreprise - utilisation diagnosticId fourni', [
                'diagnosticId' => $diagnosticId,
                'entrepriseId' => $entrepriseId
            ]);
            $dernierDiagnostic = Diagnostic::find($diagnosticId);
        } else {
            \Log::info('evaluerProfilEntreprise - recherche dernier diagnostic terminé', [
                'entrepriseId' => $entrepriseId
            ]);
            // Sinon, chercher le dernier diagnostic terminé
            $entreprise = Entreprise::with(['diagnostics' => function($query) {
                $query->where('diagnosticstatut_id', 2) // Diagnostic terminé
                      ->latest();
            }])->findOrFail($entrepriseId);
            
            $dernierDiagnostic = $entreprise->diagnostics->first();
        }
        if (!$dernierDiagnostic) {
            \Log::warning('evaluerProfilEntreprise - aucun diagnostic trouvé', [
                'entrepriseId' => $entrepriseId,
                'diagnosticId' => $diagnosticId
            ]);
            return [
                'changement_effectue' => false,
                'message' => 'Aucun diagnostic trouvé pour cette entreprise.',
                'profil_actuel' => $entreprise->entrepriseprofil_id
            ];
        }
        
        \Log::info('evaluerProfilEntreprise - diagnostic trouvé', [
            'diagnostic_id' => $dernierDiagnostic->id,
            'diagnostic_statut' => $dernierDiagnostic->diagnosticstatut_id,
            'diagnostic_score' => $dernierDiagnostic->scoreglobal
        ]);
        
        // 🕐 Vérifier le délai écoulé depuis le dernier diagnostic
        $delaiMois = $this->calculerDelaiDepuisDernierDiagnostic($dernierDiagnostic);
        
        // Calculer les scores
        $scoresParBloc = $this->calculerScoresParBloc($dernierDiagnostic);
        $scoreGlobal = $this->calculerScoreGlobal($scoresParBloc);
        
        // Déterminer le profil approprié selon les scores uniquement
        $nouveauProfilId = $this->determinerProfilSelonScores(
            $scoresParBloc, 
            $scoreGlobal, 
            $entreprise->entrepriseprofil_id
        );
        
        // Mettre à jour uniquement si changement autorisé
        \Log::info('evaluerProfilEntreprise - vérification changement', [
            'force' => $force,
            'ancien_profil' => $entreprise->entrepriseprofil_id,
        ]);
        
        // Mettre à jour uniquement si changement
        \Log::info('evaluerProfilEntreprise - vérification changement', [
            'force' => $force,
            'ancien_profil' => $entreprise->entrepriseprofil_id,
            'nouveau_profil' => $nouveauProfilId
        ]);
        
        if ($force || $this->changementAutorise($entreprise->entrepriseprofil_id, $nouveauProfilId, 0)) {
            \Log::info('evaluerProfilEntreprise - mise à jour profil', [
                'entreprise_id' => $entrepriseId,
                'ancien_profil' => $entreprise->entrepriseprofil_id,
                'nouveau_profil' => $nouveauProfilId
            ]);
            
            $ancienProfilId = $entreprise->entrepriseprofil_id;
            $entreprise->update(['entrepriseprofil_id' => $nouveauProfilId]);
            
            // Créer une évolution pour le changement de profil
            Diagnosticevolution::creerEvolution(
                $entrepriseId,
                $dernierDiagnostic->id,
                null, // Pas de diagnostic précédent spécifique pour le changement de profil
                "Changement de profil: {$this->getProfilLibelle($ancienProfilId)} → {$this->getProfilLibelle($nouveauProfilId)}"
            );
            
            return [
                'changement_effectue' => true,
                'ancien_profil' => $ancienProfilId,
                'nouveau_profil' => $nouveauProfilId,
                'score_global' => $scoreGlobal,
                'message' => $this->genererMessageSucces($ancienProfilId, $nouveauProfilId)
            ];
        }
        
        // Créer systématiquement une évolution même si pas de changement de profil
        \Log::info('evaluerProfilEntreprise - création évolution systématique', [
            'entreprise_id' => $entrepriseId,
            'diagnostic_id' => $dernierDiagnostic->id,
            'ancien_profil' => $entreprise->entrepriseprofil_id,
            'nouveau_profil' => $nouveauProfilId,
            'raison' => 'Archivage systématique du diagnostic'
        ]);
        
        Diagnosticevolution::creerEvolution(
            $entrepriseId,
            $dernierDiagnostic->id,
            null, // Pas de diagnostic précédent spécifique
            "Diagnostic finalisé - Archivage systématique"
        );
        
        \Log::info('evaluerProfilEntreprise - pas de changement', [
            'raison' => 'création évolution systématique',
            'entreprise_id' => $entrepriseId,
            'ancien_profil' => $entreprise->entrepriseprofil_id,
            'nouveau_profil' => $nouveauProfilId
        ]);
        
        return [
            'changement_effectue' => false,
            'profil_actuel' => $entreprise->entrepriseprofil_id,
            'profil_cible' => $nouveauProfilId,
            'score_global' => $scoreGlobal,
            'message' => 'Diagnostic archivé systématiquement'
        ];
    }

    /**
     * Calculer les scores par bloc pour un diagnostic
     */
    public function calculerScoresParBloc($diagnostic)
    {
        $scoresParBloc = [];
        $scoresParNiveau = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0]; // Par niveau de performance

        foreach ($diagnostic->diagnosticmodulescores as $moduleScore) {
            $blocCode = $moduleScore->diagnosticblocstatut ? $moduleScore->diagnosticblocstatut->code : 'intermediaire';
            $niveau = $moduleScore->diagnosticblocstatut ? $moduleScore->diagnosticblocstatut->getNiveauPerformance() : 2;
            
            if (!isset($scoresParBloc[$blocCode])) {
                $scoresParBloc[$blocCode] = 0;
            }
            
            $scoresParBloc[$blocCode] += $moduleScore->score;
            $scoresParNiveau[$niveau] += $moduleScore->score;
        }

        // Ajouter les scores par niveau pour l'évaluation
        $scoresParBloc['par_niveau'] = $scoresParNiveau;
        $scoresParBloc['nb_blocs_critiques'] = Diagnosticblocstatut::getByNiveau(0)->count();
        $scoresParBloc['nb_blocs_reference'] = Diagnosticblocstatut::getByNiveau(4)->count();

        // 🎯 Ajouter les métriques spécifiques aux profils PÉPITE/ÉMERGENTE/ÉLITE
        $scoresParBloc['nb_blocs_critiques_score'] = collect($scoresParBloc)->filter(fn($score, $key) => !in_array($key, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference']) && $score < 8)->count();
        $scoresParBloc['nb_blocs_conformes'] = collect($scoresParBloc)->filter(fn($score, $key) => !in_array($key, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference']) && $score >= 16)->count();
        $scoresParBloc['nb_blocs_elite'] = collect($scoresParBloc)->filter(fn($score, $key) => !in_array($key, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference']) && $score >= 18)->count();

        return $scoresParBloc;
    }

    /**
     * Calculer le score global
     */
    private function calculerScoreGlobal($scoresParBloc)
    {
        // Exclure les méta-données du calcul
        $scores = array_filter($scoresParBloc, function($key) {
            return !in_array($key, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference']);
        }, ARRAY_FILTER_USE_KEY);
        
        return array_sum($scores);
    }

    /**
     * Trouver le statut approprié selon les règles
     */
    private function trouverStatutSelonRegles($scoresParBloc, $scoreGlobal, $diagnostic)
    {
        // Les règles sont maintenant par bloc/module, pas par statut
        // On utilise une logique par défaut pour les statuts
        if ($scoreGlobal >= 80) {
            return Diagnosticstatut::where('titre', 'Éligible')->first();
        } elseif ($scoreGlobal >= 60) {
            return Diagnosticstatut::where('titre', 'Éligible conditionnel')->first();
        } elseif ($scoreGlobal >= 40) {
            return Diagnosticstatut::where('titre', 'À revoir')->first();
        } else {
            return Diagnosticstatut::where('titre', 'Non éligible')->first();
        }
    }

    /**
     * Calculer la durée du diagnostic en mois
     */
    private function calculerDureeDiagnostic($diagnostic)
    {
        if ($diagnostic->created_at) {
            return $diagnostic->created_at->diffInMonths(now());
        }
        return 0;
    }

    /**
     * Obtenir les orientations pour un diagnostic
     */
    public function getOrientationsDiagnostic($diagnosticId)
    {
        $diagnostic = Diagnostic::with(['diagnosticmodulescores.diagnosticmodule'])->findOrFail($diagnosticId);
        $scoresParBloc = $this->calculerScoresParBloc($diagnostic);
        $orientations = [];

        // Obtenir les orientations par bloc
        foreach ($scoresParBloc as $blocCode => $scoreBloc) {
            if (in_array($blocCode, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference'])) {
                continue;
            }
            
            $bloc = Diagnosticblocstatut::where('code', $blocCode)->first();
            if ($bloc) {
                $orientationsBloc = Diagnosticorientation::where('diagnosticblocstatut_id', $bloc->id)
                    ->where('seuil_max', '>=', $scoreBloc)
                    ->orderBy('seuil_max', 'asc')
                    ->get();

                if ($orientationsBloc->isNotEmpty()) {
                    $orientations[] = [
                        'bloc' => $blocCode,
                        'score' => $scoreBloc,
                        'orientations' => $orientationsBloc,
                    ];
                }
            }
        }

        return $orientations;
    }


    /**
     * Forcer la réévaluation de tous les diagnostics
     */
    public function reevaluerTousLesDiagnostics()
    {
        $diagnostics = Diagnostic::whereNotNull('entreprise_id')->get();
        $resultats = [];

        foreach ($diagnostics as $diagnostic) {
            $resultat = $this->evaluerStatutDiagnostic($diagnostic->id, true);
            $resultats[] = [
                'diagnostic_id' => $diagnostic->id,
                'entreprise' => $diagnostic->entreprise->nom ?? 'N/A',
                'resultat' => $resultat,
            ];
        }

        return $resultats;
    }

    /**
     * Obtenir les statistiques des statuts
     */
    public function getStatistiquesStatuts()
    {
        return Diagnostic::selectRaw('diagnosticstatut_id, COUNT(*) as count')
            ->with('diagnosticstatut')
            ->groupBy('diagnosticstatut_id')
            ->get()
            ->map(function($item) {
                return [
                    'statut' => $item->diagnosticstatut ? $item->diagnosticstatut->titre : 'Non défini',
                    'count' => $item->count,
                ];
            });
    }

    /**
     * Créer les blocs de statut principaux
     */
    public function initialiserBlocsStatuts()
    {
        Diagnosticblocstatut::creerBlocsPrincipaux();
    }

    // 🎯 ===== MÉTHODES POUR LA GESTION DES PROFILS D'ENTREPRISE =====

    /**
     * Calculer le délai écoulé depuis le dernier diagnostic
     */
    private function calculerDelaiDepuisDernierDiagnostic($diagnostic)
    {
        if ($diagnostic->created_at) {
            return $diagnostic->created_at->diffInMonths(now());
        }
        return 0;
    }

    /**
     * Déterminer le profil selon les scores uniquement (sans délai)
     * avec gestion des modules bloquants
     */
    private function determinerProfilSelonScores($scoresParBloc, $scoreGlobal, $profilActuel)
    {
        // 📊 Scores actuels
        $nbBlocsCritiques = $scoresParBloc['nb_blocs_critiques_score'] ?? 0;
        $nbBlocsConformes = $scoresParBloc['nb_blocs_conformes'] ?? 0;
        $blocJuridique = $scoresParBloc['JURIDIQUE'] ?? 0;
        $blocFinance = $scoresParBloc['FINANCE'] ?? 0;
        
        // 🚨 VÉRIFICATION DES MODULES BLOQUANTS - PRIORITÉ ABSOLUE
        $resultatBloquant = $this->verifierModulesBloquants($scoresParBloc, $profilActuel);
        
        if ($resultatBloquant['bloque']) {
            \Log::info('Module bloquant détecté - application règle', [
                'profil_actuel' => $profilActuel,
                'module_bloquant' => $resultatBloquant['module'],
                'score_bloquant' => $resultatBloquant['score'],
                'resultat' => $resultatBloquant['resultat'],
                'raison' => $resultatBloquant['raison']
            ]);
            
            return $resultatBloquant['resultat'];
        }
        
        // 🚨 Vérification rétrogradation standard (si pas de blocage bloquant)
        if ($profilActuel == 3) { // ÉLITE
            if ($scoreGlobal < 160 || $nbBlocsConformes < 10 || $blocJuridique < 16 || $blocFinance < 16) {
                return 2; // Rétrogradation vers ÉMERGENTE
            }
        }
        
        if ($profilActuel == 2) { // ÉMERGENTE
            if ($scoreGlobal < 120 || $nbBlocsCritiques >= 2 || $blocJuridique < 12 || $blocFinance < 12) {
                return 1; // Rétrogradation vers PÉPITE
            }
        }
        
        // 📈 Vérification progression (sans délais)
        if ($profilActuel == 1) { // PÉPITE → ÉMERGENTE
            if ($scoreGlobal >= 160 && 
                $nbBlocsConformes >= 7 && 
                $blocJuridique >= 14 && 
                $blocFinance >= 14) {
                return 2; // Progression vers ÉMERGENTE
            }
        }
        
        if ($profilActuel == 2) { // ÉMERGENTE → ÉLITE
            if ($scoreGlobal >= 160 && 
                $nbBlocsConformes >= 10 && // 100% des blocs
                $blocJuridique >= 16 && 
                $blocFinance >= 16 &&
                $this->aucunBlocInferieur($scoresParBloc, 16)) {
                return 3; // Progression vers ÉLITE
            }
        }
        
        // 🔒 Pas de changement
        return $profilActuel;
    }

    /**
     * Vérifier les règles des modules bloquants
     */
    private function verifierModulesBloquants($scoresParBloc, $profilActuel)
    {
        // Récupérer tous les modules bloquants avec leurs scores
        $modulesBloquants = $this->getModulesBloquantsAvecScores($scoresParBloc);
        
        foreach ($modulesBloquants as $module) {
            $score = $module['score'];
            $moduleNom = $module['nom'];
            
            switch ($profilActuel) {
                case 1: // PÉPITE
                    if ($score < 8) {
                        return [
                            'bloque' => true,
                            'module' => $moduleNom,
                            'score' => $score,
                            'resultat' => 1, // Reste PÉPITE 1
                            'raison' => "Module bloquant < 8 points : blocage progression PÉPITE"
                        ];
                    }
                    break;
                    
                case 2: // ÉMERGENTE
                    if ($score < 8) {
                        return [
                            'bloque' => true,
                            'module' => $moduleNom,
                            'score' => $score,
                            'resultat' => 1, // Rétrograde PÉPITE
                            'raison' => "Module bloquant < 8 points : rétrogradation ÉMERGENTE → PÉPITE"
                        ];
                    }
                    if ($score < 16) {
                        return [
                            'bloque' => true,
                            'module' => $moduleNom,
                            'score' => $score,
                            'resultat' => 2, // Reste ÉMERGENTE
                            'raison' => "Module bloquant < 16 points : blocage progression ÉMERGENTE → ÉLITE"
                        ];
                    }
                    break;
                    
                case 3: // ÉLITE
                    if ($score < 16) {
                        return [
                            'bloque' => true,
                            'module' => $moduleNom,
                            'score' => $score,
                            'resultat' => 2, // Rétrograde ÉMERGENTE
                            'raison' => "Module bloquant < 16 points : rétrogradation ÉLITE → ÉMERGENTE"
                        ];
                    }
                    break;
            }
        }
        
        return ['bloque' => false];
    }

    /**
     * Récupérer les modules bloquants avec leurs scores
     */
    private function getModulesBloquantsAvecScores($scoresParBloc)
    {
        $modulesBloquants = [];
        
        // Récupérer tous les modules qui ont est_bloquant = 1 dans la BDD
        $modulesBloquantsBDD = Diagnosticmodule::where('est_bloquant', 1)->get();
        
        foreach ($modulesBloquantsBDD as $module) {
            // Ajouter tous les modules bloquants avec leurs scores
            $modulesBloquants[] = [
                'nom' => $module->titre,
                'score' => 0, // Score par défaut, sera mis à jour si trouvé
                'bloc' => null,
                'module_id' => $module->id
            ];
        }
        
        \Log::info('Modules bloquants récupérés depuis BDD', [
            'modules_bloquants_trouves' => count($modulesBloquants),
            'modules_bloquants_bdd' => $modulesBloquantsBDD->pluck('titre')->toArray(),
            'scores_par_bloc' => $scoresParBloc
        ]);
        
        return $modulesBloquants;
    }

    /**
     * Déterminer le profil selon les scores et le délai
     */
    private function determinerProfilSelonScoresEtDelai($scoresParBloc, $scoreGlobal, $delaiMois, $profilActuel)
    {
        // 📊 Scores actuels
        $nbBlocsCritiques = $scoresParBloc['nb_blocs_critiques_score'] ?? 0;
        $nbBlocsConformes = $scoresParBloc['nb_blocs_conformes'] ?? 0;
        $blocJuridique = $scoresParBloc['JURIDIQUE'] ?? 0;
        $blocFinance = $scoresParBloc['FINANCE'] ?? 0;
        
        // 🚨 Vérification rétrogradation (immédiate)
        if ($profilActuel == 3) { // ÉLITE
            if ($scoreGlobal < 160 || $nbBlocsConformes < 10 || $blocJuridique < 16 || $blocFinance < 16) {
                return 2; // Rétrogradation vers ÉMERGENTE
            }
        }
        
        if ($profilActuel == 2) { // ÉMERGENTE
            if ($scoreGlobal < 120 || $nbBlocsCritiques >= 2 || $blocJuridique < 12 || $blocFinance < 12) {
                return 1; // Rétrogradation vers PÉPITE
            }
        }
        
        // 📈 Vérification progression (avec délais)
        if ($profilActuel == 1) { // PÉPITE → ÉMERGENTE
            if ($delaiMois >= 3 && 
                $scoreGlobal >= 160 && 
                $nbBlocsConformes >= 7 && 
                $blocJuridique >= 14 && 
                $blocFinance >= 14) {
                return 2; // Progression vers ÉMERGENTE
            }
        }
        
        if ($profilActuel == 2) { // ÉMERGENTE → ÉLITE
            if ($delaiMois >= 3 && 
                $scoreGlobal >= 160 && 
                $nbBlocsConformes >= 10 && // 100% des blocs
                $blocJuridique >= 16 && 
                $blocFinance >= 16 &&
                $this->aucunBlocInferieur($scoresParBloc, 16)) {
                return 3; // Progression vers ÉLITE
            }
        }
        
        // 🔒 Pas de changement
        return $profilActuel;
    }

    /**
     * Vérifier si le changement de profil est autorisé
     */
    private function changementAutorise($profilActuel, $nouveauProfil, $delaiMois)
    {
        // 🚫 Rétrogradations : toujours autorisées (immédiat)
        if ($nouveauProfil < $profilActuel) {
            return true;
        }
        
        // ⏰ Progressions : vérifier les délais minimaux
        switch ($profilActuel) {
            case 1: // PÉPITE → ÉMERGENTE
                return $delaiMois >= 3;
                
            case 2: // ÉMERGENTE → ÉLITE
                return $delaiMois >= 3;
                
            default:
                return false;
        }
    }

    /**
     * Obtenir la raison du blocage
     */
    private function getRaisonBlocage($profilActuel, $nouveauProfil, $delaiMois)
    {
        $profils = [1 => 'PÉPITE', 2 => 'ÉMERGENTE', 3 => 'ÉLITE'];
        
        if ($nouveauProfil > $profilActuel) {
            $delaiRequis = 3; // mois
            
            if ($delaiMois < $delaiRequis) {
                return "🕐 Délai minimum de {$delaiRequis} mois requis avant la progression. Actuellement : {$delaiMois} mois écoulés.";
            }
        }
        
        return "📊 Conditions de score non remplies pour la progression vers {$profils[$nouveauProfil]}.";
    }

    /**
     * Générer le message de succès
     */
    private function genererMessageSucces($ancienProfil, $nouveauProfil, $delaiMois)
    {
        $profils = [1 => 'PÉPITE', 2 => 'ÉMERGENTE', 3 => 'ÉLITE'];
        
        if ($nouveauProfil > $ancienProfil) {
            return "🎉 Félicitations ! Après {$delaiMois} mois dans le statut {$profils[$ancienProfil]} et une excellente progression, votre entreprise accède au statut {$profils[$nouveauProfil]} !";
        } else {
            return "📋 Mise à jour du profil : {$profils[$ancienProfil]} → {$profils[$nouveauProfil]}";
        }
    }

    /**
     * Vérifier si aucun bloc n'est inférieur au seuil
     */
    private function aucunBlocInferieur($scoresParBloc, $seuil)
    {
        return collect($scoresParBloc)
            ->filter(fn($score, $key) => !in_array($key, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference', 'nb_blocs_critiques_score', 'nb_blocs_conformes', 'nb_blocs_elite']))
            ->every(fn($score) => $score >= $seuil);
    }


    /**
     * Réévaluer tous les profils d'entreprise
     */
    public function reevaluerTousLesProfils()
    {
        $entreprises = Entreprise::whereNotNull('entrepriseprofil_id')->get();
        $resultats = [];

        foreach ($entreprises as $entreprise) {
            $resultat = $this->evaluerProfilEntreprise($entreprise->id, true);
            $resultats[] = [
                'entreprise_id' => $entreprise->id,
                'entreprise_nom' => $entreprise->nom,
                'resultat' => $resultat,
            ];
        }

        return $resultats;
    }

    /**
     * Obtenir les statistiques des profils
     */
    public function getStatistiquesProfils()
    {
        return Entreprise::selectRaw('entrepriseprofil_id, COUNT(*) as count')
            ->whereNotNull('entrepriseprofil_id')
            ->groupBy('entrepriseprofil_id')
            ->get()
            ->map(function($item) {
                $profils = [1 => 'PÉPITE', 2 => 'ÉMERGENTE', 3 => 'ÉLITE'];
                return [
                    'profil' => $profils[$item->entrepriseprofil_id] ?? 'Non défini',
                    'count' => $item->count,
                ];
            });
    }

    /**
     * Obtenir les évolutions pour une entreprise
     */
    public function getEvolutions($entrepriseId, $limit = 10)
    {
        return Diagnosticevolution::pourEntreprise($entrepriseId, $limit)->reverse();
    }

    /**
     * Obtenir la dernière évolution pour une entreprise
     */
    public function getDerniereEvolution($entrepriseId)
    {
        return Diagnosticevolution::dernierePourEntreprise($entrepriseId);
    }

    /**
     * Obtenir le libellé d'un profil
     */
    private function getProfilLibelle($profilId)
    {
        $profils = [1 => 'PÉPITE', 2 => 'ÉMERGENTE', 3 => 'ÉLITE'];
        return $profils[$profilId] ?? 'Non défini';
    }
}
