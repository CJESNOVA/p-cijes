<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Abonnementtype;

class AbonnementtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Abonnements mensuels, trimestriels, semestriels et annuels
        $abonnements = [
            // Abonnements (pour tous les profils)
            [
                'titre' => 'Abonnement mensuel',
                'montant' => 1000,
                'entrepriseprofil_id' => null, // Pour tous les profils
                'nombre_jours' => 30,
                'etat' => 1,
            ],
            [
                'titre' => 'Abonnement trimestriel',
                'montant' => 3000,
                'entrepriseprofil_id' => null, // Pour tous les profils
                'nombre_jours' => 90,
                'etat' => 1,
            ],
            [
                'titre' => 'Abonnement semestriel',
                'montant' => 5500,
                'entrepriseprofil_id' => null, // Pour tous les profils
                'nombre_jours' => 180,
                'etat' => 1,
            ],
            [
                'titre' => 'Abonnement annuel',
                'montant' => 10000,
                'entrepriseprofil_id' => null, // Pour tous les profils
                'nombre_jours' => 365,
                'etat' => 1,
            ],
            
            // Agréments annuels pour Prestataires Intellectuels
            [
                'titre' => 'Agrément annuel - Prestataire Intellectuel Pépite',
                'montant' => 25000,
                'entrepriseprofil_id' => 1, // PÉPITE
                'nombre_jours' => 365,
                'etat' => 1,
            ],
            [
                'titre' => 'Agrément annuel - Prestataire Intellectuel Émergeant',
                'montant' => 50000,
                'entrepriseprofil_id' => 2, // ÉMERGENTE
                'nombre_jours' => 365,
                'etat' => 1,
            ],
            [
                'titre' => 'Agrément annuel - Prestataire Intellectuel Élite',
                'montant' => 100000,
                'entrepriseprofil_id' => 3, // ÉLITE
                'nombre_jours' => 365,
                'etat' => 1,
            ],
        ];

        foreach ($abonnements as $abonnement) {
            Abonnementtype::updateOrCreate([
                'titre' => $abonnement['titre'],
                'entrepriseprofil_id' => $abonnement['entrepriseprofil_id']
            ], $abonnement);
        }

        $this->command->info('Types d\'abonnement créés avec succès!');
        $this->command->info('- 4 abonnements (mensuel, trimestriel, semestriel, annuel)');
        $this->command->info('- 3 agréments annuels pour Prestataires Intellectuels (Pépite, Émergeant, Élite)');
        $this->command->info('- Moyens de paiement acceptés: Coris, Kobo, Bon, Sika');
    }
}
