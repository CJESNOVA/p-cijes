<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Propositionstatut;

class PropositionstatutSeeder extends Seeder
{
    public function run(): void
    {
        $statuts = [
            ['titre' => 'En attente', 'etat' => 1],
            ['titre' => 'Acceptée', 'etat' => 1],
            ['titre' => 'Refusée', 'etat' => 1],
            ['titre' => 'Annulée', 'etat' => 1],
        ];

        foreach ($statuts as $statut) {
            Propositionstatut::create($statut);
        }

        $this->command->info('Statuts de proposition créés avec succès !');
    }
}
