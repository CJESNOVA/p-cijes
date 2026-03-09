<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccompagnementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Niveaux d'accompagnement
        $niveaux = [
            ['titre' => 'Initial', 'description' => 'Premier niveau d\'accompagnement', 'etat' => true],
            ['titre' => 'Intermédiaire', 'description' => 'Niveau moyen d\'accompagnement', 'etat' => true],
            ['titre' => 'Avancé', 'description' => 'Niveau avancé d\'accompagnement', 'etat' => true],
        ];

        foreach ($niveaux as $niveau) {
            DB::table('accompagnementniveaux')->insert([
                'titre' => $niveau['titre'],
                'description' => $niveau['description'],
                'etat' => $niveau['etat'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Statuts d'accompagnement
        $statuts = [
            ['titre' => 'En cours', 'description' => 'Accompagnement actuellement en cours', 'etat' => true],
            ['titre' => 'Terminé', 'description' => 'Accompagnement terminé avec succès', 'etat' => true],
            ['titre' => 'En attente', 'description' => 'Accompagnement en attente de démarrage', 'etat' => true],
            ['titre' => 'Suspendu', 'description' => 'Accompagnement temporairement suspendu', 'etat' => true],
        ];

        foreach ($statuts as $statut) {
            DB::table('accompagnementstatuts')->insert([
                'titre' => $statut['titre'],
                'description' => $statut['description'],
                'etat' => $statut['etat'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
