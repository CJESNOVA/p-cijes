<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Propositionstatut;

class PropositionstatutSeeder extends Seeder
{
    public function run(): void
    {
        $statuts = [
            ['titre' => 'En attente', 'etat' => true],
            ['titre' => 'Acceptée', 'etat' => true],
            ['titre' => 'Refusée', 'etat' => true],
            ['titre' => 'Annulée', 'etat' => true],
        ];

        foreach ($statuts as $statut) {
            Propositionstatut::create($statut);
        }

        $this->command->info('Statuts de proposition créés avec succès !');
    }
}
