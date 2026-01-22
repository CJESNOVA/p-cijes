<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Propositionstatut;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer les statuts de proposition
        $this->call(PropositionstatutSeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Piniastudio',
            'email' => 'help@piniastudio.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
    }
}
