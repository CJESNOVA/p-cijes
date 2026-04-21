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
    protected $recompenseService;

    public function __construct(RecompenseService $recompenseService = null)
    {
        $this->recompenseService = $recompenseService;
    }
    /**
     * Récupérer les règles pour un profil et type de diagnostic
     */
    private function getReglesPourProfil($profilId, $typeDiagnosticId = 2)
    {
        return Diagnosticstatutregle::where('entrepriseprofil_id', $profilId)
            ->where('diagnostictype_id', $typeDiagnosticId)
            ->orderBy('score_total_min')
            ->get();
    }

    /**
     * Vérifier les règles de blocage spécifiques
     */
    private function verifierReglesBlocage($scoresParBloc, $profilId, $typeDiagnosticId = 2)
    {
        $reglesBlocage = Diagnosticstatutregle::where('entrepriseprofil_id', $profilId)
            ->where('diagnostictype_id', $typeDiagnosticId)
            ->where(function($query) {
                $query->where('bloc_juridique_min', '>', 0)
                      ->orWhere('bloc_finance_min', '>', 0);
            })
            ->get();

        $blocJuridique = $scoresParBloc['JURIDIQUE'] ?? 0;
        $blocFinance = $scoresParBloc['FINANCE'] ?? 0;

        foreach ($reglesBlocage as $regle) {
            $blocageJuridique = $regle->bloc_juridique_min > 0 && $blocJuridique < $regle->bloc_juridique_min;
            $blocageFinance = $regle->bloc_finance_min > 0 && $blocFinance < $regle->bloc_finance_min;

            if ($blocageJuridique || $blocageFinance) {
                $typeBlocage = $blocageJuridique ? 'juridique' : 'finance';
                $seuil = $blocageJuridique ? $regle->bloc_juridique_min : $regle->bloc_finance_min;
                
                return [
                    'bloque' => true,
                    'type_blocage' => $typeBlocage,
                    'seuil' => $seuil,
                    'score_actuel' => $typeBlocage === 'juridique' ? $blocJuridique : $blocFinance,
                    'regle_id' => $regle->id,
                    'raison' => "Bloc {$typeBlocage} < {$seuil} points"
                ];
            }
        }

        return ['bloque' => false];
    }

    /**
     * Déterminer le profil selon les règles de la base de données
     */
    private function determinerProfilSelonRegles($scoresParBloc, $scoreGlobal, $profilActuel, $typeDiagnosticId = 2)
    {
        // 🚨 Vérifier d'abord les règles de blocage
        $blocage = $this->verifierReglesBlocage($scoresParBloc, $profilActuel, $typeDiagnosticId);
        
        if ($blocage['bloque']) {
            \Log::info('Blocage détecté selon règle BDD', [
                'profil_actuel' => $profilActuel,
                'type_blocage' => $blocage['type_blocage'],
                'seuil' => $blocage['seuil'],
                'score_actuel' => $blocage['score_actuel'],
                'regle_id' => $blocage['regle_id']
            ]);
            
            // En cas de blocage, déterminer le profil résultant
            if ($profilActuel == 3) { // ÉLITE -> rétrograde ÉMERGENTE
                return 2;
            } elseif ($profilActuel == 2) { // ÉMERGENTE -> rétrograde PÉPITE
                return 1;
            }
            // PÉPITE reste PÉPITE en cas de blocage
            return 1;
        }

        // 📈 Vérifier les règles de progression par profil cible
        $profilsATester = [1, 2, 3]; // PÉPITE, ÉMERGENTE, ÉLITE
        
        foreach ($profilsATester as $profilCible) {
            if ($profilCible <= $profilActuel) {
                continue; // Ne pas tester les profils inférieurs ou égaux
            }

            $reglesProfil = $this->getReglesPourProfil($profilCible, $typeDiagnosticId);
            
            foreach ($reglesProfil as $regle) {
                if ($this->verifierConditionRegle($regle, $scoresParBloc, $scoreGlobal)) {
                    \Log::info('Progression selon règle BDD', [
                        'profil_actuel' => $profilActuel,
                        'profil_cible' => $profilCible,
                        'regle_id' => $regle->id,
                        'score_global' => $scoreGlobal,
                        'condition_remplie' => true
                    ]);
                    
                    return $profilCible;
                }
            }
        }

        // 🚨 Vérifier les règles de rétrogradation
        foreach ($profilsATester as $profilCible) {
            if ($profilCible >= $profilActuel) {
                continue; // Ne pas tester les profils supérieurs ou égaux
            }

            $reglesProfil = $this->getReglesPourProfil($profilCible, $typeDiagnosticId);
            
            foreach ($reglesProfil as $regle) {
                if (!$this->verifierConditionRegle($regle, $scoresParBloc, $scoreGlobal)) {
                    \Log::info('Rétrogradation selon règle BDD', [
                        'profil_actuel' => $profilActuel,
                        'profil_cible' => $profilCible,
                        'regle_id' => $regle->id,
                        'score_global' => $scoreGlobal,
                        'condition_non_remplie' => true
                    ]);
                    
                    return $profilCible;
                }
            }
        }

        // 🔒 Pas de changement
        return $profilActuel;
    }

    /**
     * Vérifier si une condition de règle est remplie
     */
    private function verifierConditionRegle($regle, $scoresParBloc, $scoreGlobal)
    {
        // Vérifier le score total
        if ($regle->score_total_min !== null && $scoreGlobal < $regle->score_total_min) {
            return false;
        }
        
        if ($regle->score_total_max !== null && $scoreGlobal > $regle->score_total_max) {
            return false;
        }

        // Vérifier le nombre minimum de blocs avec score
        if ($regle->min_blocs_score > 0) {
            $nbBlocsAvecScore = collect($scoresParBloc)
                ->filter(fn($score, $key) => !in_array($key, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference', 'nb_blocs_critiques_score', 'nb_blocs_conformes', 'nb_blocs_elite']) && $score >= ($regle->min_score_bloc ?? 0))
                ->count();
            
            if ($nbBlocsAvecScore < $regle->min_blocs_score) {
                return false;
            }
        }

        // Vérifier les blocs juridique et finance
        $blocJuridique = $scoresParBloc['JURIDIQUE'] ?? 0;
        $blocFinance = $scoresParBloc['FINANCE'] ?? 0;
        
        if ($regle->bloc_juridique_min > 0 && $blocJuridique < $regle->bloc_juridique_min) {
            return false;
        }
        
        if ($regle->bloc_finance_min > 0 && $blocFinance < $regle->bloc_finance_min) {
            return false;
        }

        // Vérifier qu'aucun bloc n'est inférieur au seuil
        if ($regle->aucun_bloc_inf > 0) {
            $blocInferieur = collect($scoresParBloc)
                ->filter(fn($score, $key) => !in_array($key, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference', 'nb_blocs_critiques_score', 'nb_blocs_conformes', 'nb_blocs_elite']) && $score < $regle->aucun_bloc_inf)
                ->isNotEmpty();
            
            if ($blocInferieur) {
                return false;
            }
        }

        return true;
    }

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

            // 🎁 INSÉRER RÉCOMPENSE ICI - Progression de diagnostic statut
            if ($ancienStatutId && $ancienStatutId < $nouveauStatut->id && $diagnostic->entreprise) {
                // Logique de récompense pour PASSAGE_NIVEAU
                $membre = $this->getMembrePrincipalEntreprise($diagnostic->entreprise);
                if ($membre) {
                    // 💡 Utiliser le score global comme montant pour le calcul en pourcentage
                    $this->recompenseService->attribuerRecompense('PASSAGE_NIVEAU', $membre, $diagnostic->entreprise, $diagnostic->id, null);
                }
            }

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
                    $derniereEvolution ? $derniereEvolution->diagnostic_id : 0,
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
        
        // Déterminer le profil approprié selon les règles de la base de données
        $nouveauProfilId = $this->determinerProfilSelonRegles(
            $scoresParBloc, 
            $scoreGlobal, 
            $entreprise->entrepriseprofil_id,
            2 // Type diagnostic entreprise
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
        
        if ($force || $this->changementAutorise($entreprise->entrepriseprofil_id, $nouveauProfilId, 0, 2)) {
            \Log::info('evaluerProfilEntreprise - mise à jour profil', [
                'entreprise_id' => $entrepriseId,
                'ancien_profil' => $entreprise->entrepriseprofil_id,
                'nouveau_profil' => $nouveauProfilId
            ]);
            
            $ancienProfilId = $entreprise->entrepriseprofil_id;

            // 🎁 INSÉRER RÉCOMPENSE ICI - Progression de profil d'entreprise
            if ($ancienProfilId < $nouveauProfilId) {
                // Logique de récompense pour PASSAGE_PROFIL
                $membre = $this->getMembrePrincipalEntreprise($entreprise);
                if ($membre) {
                    // 💡 Utiliser le score global comme montant pour le calcul en pourcentage
                    $this->recompenseService->attribuerRecompense('PASSAGE_PROFIL', $membre, $entreprise, $dernierDiagnostic->id, null);
                }
            }
 
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
                'message' => $this->genererMessageSucces($ancienProfilId, $nouveauProfilId, 0)
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
     * Obtenir la raison du blocage selon les règles
     */
    private function getRaisonBlocage($profilActuel, $nouveauProfil, $delaiMois)
    {
        $profils = [1 => 'PÉPITE', 2 => 'ÉMERGENTE', 3 => 'ÉLITE'];
        
        if ($nouveauProfil > $profilActuel) {
            return "🕐 Délai minimum requis avant la progression vers {$profils[$nouveauProfil]}. Actuellement : {$delaiMois} mois écoulés.";
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
     * Vérifier si le changement de profil est autorisé selon les règles
     */
    private function changementAutorise($profilActuel, $nouveauProfil, $delaiMois, $typeDiagnosticId = 2)
    {
        // 🚫 Rétrogradations : toujours autorisées (immédiat)
        if ($nouveauProfil < $profilActuel) {
            return true;
        }
        
        // ⏰ Progressions : vérifier les délais minimaux selon les règles
        $reglesProfilCible = $this->getReglesPourProfil($nouveauProfil, $typeDiagnosticId);
        
        foreach ($reglesProfilCible as $regle) {
            // Vérifier si cette règle correspond à une condition de progression
            if ($this->verifierConditionRegle($regle, [], 999999)) { // Score élevé pour forcer la vérification
                $delaiRequis = $regle->duree_min_mois;
                
                \Log::info('Vérification délai progression', [
                    'profil_actuel' => $profilActuel,
                    'profil_cible' => $nouveauProfil,
                    'delai_requis' => $delaiRequis,
                    'delai_actuel' => $delaiMois,
                    'regle_id' => $regle->id
                ]);
                
                return $delaiMois >= $delaiRequis;
            }
        }
        
        // Si aucune règle spécifique trouvée, utiliser le délai par défaut
        return $delaiMois >= 3;
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

    /**
     * Récupérer le membre principal d'une entreprise
     */
    private function getMembrePrincipalEntreprise($entreprise)
    {
        if (!$entreprise) {
            return null;
        }

        // Chercher le premier membre actif de l'entreprise
        return $entreprise->entreprisesmembres()
            ->with('membre')
            ->whereHas('membre', function ($query) {
                $query->where('etat', 1);
            })
            ->first()
            ?->membre;
    }
}
