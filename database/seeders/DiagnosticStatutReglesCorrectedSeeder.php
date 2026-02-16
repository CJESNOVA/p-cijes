<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnosticstatut;
use App\Models\Diagnosticstatutregle;
use App\Models\Diagnosticblocstatut;
use App\Models\Diagnosticmodule;

class DiagnosticStatutReglesCorrectedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les statuts existants
        $statutNonEvalue = Diagnosticstatut::where('titre', 'Non évalué')->first();
        $statutEligible = Diagnosticstatut::where('titre', 'Éligible')->first();
        $statutNonEligible = Diagnosticstatut::where('titre', 'Non éligible')->first();
        $statutConditionnel = Diagnosticstatut::where('titre', 'Éligible conditionnel')->first();
        $statutARevoir = Diagnosticstatut::where('titre', 'À revoir')->first();

        // Récupérer les blocs
        $blocStrategie = Diagnosticblocstatut::where('code', 'STRATEGIE')->first();
        $blocFinance = Diagnosticblocstatut::where('code', 'FINANCE')->first();
        $blocJuridique = Diagnosticblocstatut::where('code', 'JURIDIQUE')->first();
        $blocRH = Diagnosticblocstatut::where('code', 'RH')->first();
        $blocMarketing = Diagnosticblocstatut::where('code', 'MARKETING')->first();
        $blocCommunication = Diagnosticblocstatut::where('code', 'COMMUNICATION')->first();
        $blocCommercial = Diagnosticblocstatut::where('code', 'COMMERCIAL')->first();
        $blocOperationnel = Diagnosticblocstatut::where('code', 'OPERATIONNEL')->first();
        $blocDigital = Diagnosticblocstatut::where('code', 'DIGITAL')->first();
        $blocAdministration = Diagnosticblocstatut::where('code', 'ADMINISTRATION')->first();

        // Supprimer les anciennes règles incorrectes
        Diagnosticstatutregle::truncate();

        // 🎯 Règles globales (tous blocs)
        $reglesGlobales = [
            // Règle pour Éligible
            [
                'diagnosticstatut_id' => $statutEligible->id,
                'score_total_min' => 80,
                'min_blocs_score' => 4,
                'min_score_bloc' => 15,
                'aucun_bloc_inf' => 10,
            ],
            // Règle pour Éligible conditionnel
            [
                'diagnosticstatut_id' => $statutConditionnel->id,
                'score_total_min' => 60,
                'score_total_max' => 79,
                'min_blocs_score' => 3,
                'min_score_bloc' => 12,
            ],
            // Règle pour À revoir
            [
                'diagnosticstatut_id' => $statutARevoir->id,
                'score_total_min' => 40,
                'score_total_max' => 59,
                'duree_min_mois' => 3,
            ],
            // Règle pour Non éligible
            [
                'diagnosticstatut_id' => $statutNonEligible->id,
                'score_total_max' => 39,
            ],
        ];

        foreach ($reglesGlobales as $regle) {
            Diagnosticstatutregle::create($regle);
        }

        // 🎯 Règles spécifiques par bloc (exemples)
        $reglesParBloc = [
            // Bloc Stratégie - Éligible si score ≥ 15
            [
                'diagnosticstatut_id' => $statutEligible->id,
                'diagnosticblocstatut_id' => $blocStrategie->id,
                'score_total_min' => 15,
            ],
            // Bloc Finance - Éligible si score ≥ 16 (plus strict)
            [
                'diagnosticstatut_id' => $statutEligible->id,
                'diagnosticblocstatut_id' => $blocFinance->id,
                'score_total_min' => 16,
            ],
            // Bloc Juridique - Éligible si score ≥ 14 (bloquant)
            [
                'diagnosticstatut_id' => $statutEligible->id,
                'diagnosticblocstatut_id' => $blocJuridique->id,
                'score_total_min' => 14,
            ],
            // Bloc RH - Éligible conditionnel si score ≥ 12
            [
                'diagnosticstatut_id' => $statutConditionnel->id,
                'diagnosticblocstatut_id' => $blocRH->id,
                'score_total_min' => 12,
                'score_total_max' => 15,
            ],
        ];

        foreach ($reglesParBloc as $regle) {
            Diagnosticstatutregle::create($regle);
        }

        // 🎯 Règles spécifiques par module (exemples)
        $modules = Diagnosticmodule::where('etat', 1)->limit(5)->get();
        
        foreach ($modules as $module) {
            // Règle pour chaque module - Éligible si score ≥ 8
            Diagnosticstatutregle::create([
                'diagnosticstatut_id' => $statutEligible->id,
                'diagnosticmodule_id' => $module->id,
                'score_total_min' => 8,
            ]);
            
            // Règle pour chaque module - Non éligible si score < 5
            Diagnosticstatutregle::create([
                'diagnosticstatut_id' => $statutNonEligible->id,
                'diagnosticmodule_id' => $module->id,
                'score_total_max' => 4,
            ]);
        }

        $this->command->info('Règles de diagnostic corrigées créées avec succès !');
        $this->command->info('Structure : diagnosticstatut_id + diagnosticblocstatut_id + diagnosticmodule_id');
    }
}
