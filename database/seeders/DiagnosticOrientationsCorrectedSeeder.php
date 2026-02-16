<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnosticorientation;
use App\Models\Diagnosticmodule;
use App\Models\Diagnosticblocstatut;

class DiagnosticOrientationsCorrectedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        // Récupérer quelques modules pour les orientations spécifiques
        $modules = Diagnosticmodule::where('etat', 1)->limit(5)->get();

        // Supprimer les anciennes orientations incorrectes
        Diagnosticorientation::truncate();

        // 🎯 Orientations par bloc (selon votre documentation)

        // Bloc Stratégie
        $orientationsStrategie = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocStrategie->id,
                'seuil_max' => 7,
                'dispositif' => 'CIJET – Structuration stratégique',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocStrategie->id,
                'seuil_max' => 15,
                'dispositif' => 'CIJET – stratégie & gouvernance',
            ],
        ];

        // Bloc Finance & Comptabilité
        $orientationsFinance = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocFinance->id,
                'seuil_max' => 7,
                'dispositif' => 'CGA / comptabilité simplifiée',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocFinance->id,
                'seuil_max' => 15,
                'dispositif' => 'CGA / préparation financement',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocFinance->id,
                'seuil_max' => 20,
                'dispositif' => 'Accès financement structuré (banques, investisseurs)',
            ],
        ];

        // Bloc Juridique
        $orientationsJuridique = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocJuridique->id,
                'seuil_max' => 7,
                'dispositif' => 'Formalisation / RCCM / NIF',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocJuridique->id,
                'seuil_max' => 15,
                'dispositif' => 'Mise en conformité avancée',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocJuridique->id,
                'seuil_max' => 20,
                'dispositif' => 'Structuration juridique avancée (holding, filiales)',
            ],
        ];

        // Bloc Ressources Humaines
        $orientationsRH = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocRH->id,
                'seuil_max' => 7,
                'dispositif' => 'Mise en place RH de base',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocRH->id,
                'seuil_max' => 15,
                'dispositif' => 'Structuration RH & social',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocRH->id,
                'seuil_max' => 20,
                'dispositif' => 'Leadership & gouvernance RH',
            ],
        ];

        // Bloc Marketing
        $orientationsMarketing = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocMarketing->id,
                'seuil_max' => 7,
                'dispositif' => 'Positionnement & offre',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocMarketing->id,
                'seuil_max' => 15,
                'dispositif' => 'Positionnement & branding',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocMarketing->id,
                'seuil_max' => 20,
                'dispositif' => 'Branding national / régional',
            ],
        ];

        // Bloc Commercial
        $orientationsCommercial = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocCommercial->id,
                'seuil_max' => 7,
                'dispositif' => 'Sous traitance Premiers Deals',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocCommercial->id,
                'seuil_max' => 15,
                'dispositif' => 'Premiers Deals avancé',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocCommercial->id,
                'seuil_max' => 20,
                'dispositif' => 'Accès marchés structurés (grands comptes, B2G)',
            ],
        ];

        // Bloc Opérations
        $orientationsOperationnel = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocOperationnel->id,
                'seuil_max' => 7,
                'dispositif' => 'Organisation & process',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocOperationnel->id,
                'seuil_max' => 15,
                'dispositif' => 'Optimisation & process',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocOperationnel->id,
                'seuil_max' => 20,
                'dispositif' => 'Passage à l\'échelle & excellence opérationnelle',
            ],
        ];

        // Bloc Digital
        $orientationsDigital = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocDigital->id,
                'seuil_max' => 7,
                'dispositif' => 'Digitalisation de base',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocDigital->id,
                'seuil_max' => 15,
                'dispositif' => 'Digitalisation & outils',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocDigital->id,
                'seuil_max' => 20,
                'dispositif' => 'Transformation digitale avancée',
            ],
        ];

        // Bloc Administration
        $orientationsAdministration = [
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocAdministration->id,
                'seuil_max' => 7,
                'dispositif' => 'Structuration administrative',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocAdministration->id,
                'seuil_max' => 15,
                'dispositif' => 'Gouvernance & reporting',
            ],
            [
                'diagnosticmodule_id' => null,
                'diagnosticblocstatut_id' => $blocAdministration->id,
                'seuil_max' => 20,
                'dispositif' => 'Gouvernance multi-activités & reporting stratégique',
            ],
        ];

        // Insérer toutes les orientations par bloc
        $allOrientations = array_merge(
            $orientationsStrategie,
            $orientationsFinance,
            $orientationsJuridique,
            $orientationsRH,
            $orientationsMarketing,
            $orientationsCommercial,
            $orientationsOperationnel,
            $orientationsDigital,
            $orientationsAdministration
        );

        foreach ($allOrientations as $orientation) {
            Diagnosticorientation::create($orientation);
        }

        // 🎯 Orientations spécifiques par module (exemples)
        foreach ($modules as $module) {
            Diagnosticorientation::create([
                'diagnosticmodule_id' => $module->id,
                'diagnosticblocstatut_id' => null,
                'seuil_max' => 8,
                'dispositif' => "Accompagnement spécifique - Module : {$module->titre}",
            ]);
            
            Diagnosticorientation::create([
                'diagnosticmodule_id' => $module->id,
                'diagnosticblocstatut_id' => null,
                'seuil_max' => 16,
                'dispositif' => "Accompagnement avancé - Module : {$module->titre}",
            ]);
        }

        $this->command->info('Orientations de diagnostic corrigées créées avec succès !');
        $this->command->info('Structure : diagnosticblocstatut_id au lieu de diagnosticstatut_id');
        $this->command->info('Types d\'orientations : par bloc + par module');
    }
}
