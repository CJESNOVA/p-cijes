<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entrepriseprofil;

class EntrepriseProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les profils d'entreprise PÉPITE/ÉMERGENTE/ÉLITE
        $profils = [
            [
                'id' => 1,
                'titre' => 'PÉPITE',
                'description' => 'Entreprise en phase de structuration initiale',
                'couleur' => '#f97316', // orange
                'icone' => 'seedling',
                'niveau' => 1,
                'etat' => 1,
            ],
            [
                'id' => 2,
                'titre' => 'ÉMERGENTE',
                'description' => 'Entreprise en phase de consolidation et de croissance',
                'couleur' => '#22c55e', // vert
                'icone' => 'trending-up',
                'niveau' => 2,
                'etat' => 1,
            ],
            [
                'id' => 3,
                'titre' => 'ÉLITE',
                'description' => 'Entreprise mature prête pour l\'expansion et les marchés majeurs',
                'couleur' => '#3b82f6', // bleu
                'icone' => 'crown',
                'niveau' => 3,
                'etat' => 1,
            ],
        ];

        foreach ($profils as $profil) {
            Entrepriseprofil::updateOrCreate(['id' => $profil['id']], $profil);
        }

        $this->command->info('Profils d\'entreprise (PÉPITE/ÉMERGENTE/ÉLITE) créés avec succès!');
    }
}
