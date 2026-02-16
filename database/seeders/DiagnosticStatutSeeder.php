<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnosticstatut;
use App\Models\Diagnosticstatutregle;
use App\Models\Diagnosticblocstatut;
use App\Models\Diagnosticorientation;
use App\Models\Diagnosticmodule;

class DiagnosticStatutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les statuts de diagnostic
        $statuts = [
            ['titre' => 'Non évalué', 'etat' => 1],
            ['titre' => 'Éligible', 'etat' => 1],
            ['titre' => 'Non éligible', 'etat' => 1],
            ['titre' => 'Éligible conditionnel', 'etat' => 1],
            ['titre' => 'À revoir', 'etat' => 1],
        ];

        foreach ($statuts as $statut) {
            Diagnosticstatut::create($statut);
        }

        // Créer les blocs de statut
        $blocs = [
            ['code' => 'JURIDIQUE', 'titre' => 'Bloc Juridique', 'description' => 'Questions juridiques et réglementaires'],
            ['code' => 'FINANCE', 'titre' => 'Bloc Finance', 'description' => 'Questions financières et comptables'],
            ['code' => 'RH', 'titre' => 'Bloc Ressources Humaines', 'description' => 'Questions RH et sociales'],
            ['code' => 'STRATEGIE', 'titre' => 'Bloc Stratégie', 'description' => 'Questions stratégiques et organisationnelles'],
            ['code' => 'OPERATIONNEL', 'titre' => 'Bloc Opérationnel', 'description' => 'Questions opérationnelles et process'],
            ['code' => 'DIGITAL', 'titre' => 'Bloc Digital', 'description' => 'Questions numériques et technologiques'],
            ['code' => 'COMMERCIAL', 'titre' => 'Bloc Commercial', 'description' => 'Questions commerciales et marketing'],
        ];

        foreach ($blocs as $bloc) {
            Diagnosticblocstatut::create($bloc);
        }

        // Créer les règles de statut
        $statutEligible = Diagnosticstatut::where('titre', 'Éligible')->first();
        $statutNonEligible = Diagnosticstatut::where('titre', 'Non éligible')->first();
        $statutConditionnel = Diagnosticstatut::where('titre', 'Éligible conditionnel')->first();
        $statutARevoir = Diagnosticstatut::where('titre', 'À revoir')->first();
        $statutNonEvalue = Diagnosticstatut::where('titre', 'Non évalué')->first();

        $regles = [
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

        foreach ($regles as $regle) {
            Diagnosticstatutregle::create($regle);
        }

        // Créer quelques orientations d'exemple
        $modules = Diagnosticmodule::where('etat', 1)->limit(5)->get();
        
        if ($modules->isNotEmpty()) {
            foreach ($modules as $index => $module) {
                // Orientations pour le statut Éligible
                Diagnosticorientation::create([
                    'diagnosticmodule_id' => $module->id,
                    'diagnosticstatut_id' => $statutEligible->id,
                    'seuil_max' => 100,
                    'dispositif' => 'Accompagnement complet - Package Premium',
                ]);

                // Orientations pour le statut Éligible conditionnel
                Diagnosticorientation::create([
                    'diagnosticmodule_id' => $module->id,
                    'diagnosticstatut_id' => $statutConditionnel->id,
                    'seuil_max' => 79,
                    'dispositif' => 'Accompagnement modulé - Package Standard',
                ]);

                // Orientations pour le statut À revoir
                Diagnosticorientation::create([
                    'diagnosticmodule_id' => $module->id,
                    'diagnosticstatut_id' => $statutARevoir->id,
                    'seuil_max' => 59,
                    'dispositif' => 'Pré-diagnostic - Report de 3 mois',
                ]);

                // Orientations pour le statut Non éligible
                Diagnosticorientation::create([
                    'diagnosticmodule_id' => $module->id,
                    'diagnosticstatut_id' => $statutNonEligible->id,
                    'seuil_max' => 39,
                    'dispositif' => 'Orientation vers partenaires externes',
                ]);
            }
        }

        $this->command->info('Statuts de diagnostic et règles créés avec succès!');
    }
}
