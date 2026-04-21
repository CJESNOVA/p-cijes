<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiagnosticBlocStatutSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('diagnosticblocstatuts')->insert([
            [
                'id' => 1,
                'code' => 'critique',
                'titre' => 'Bloc critique',
                'description' => 'Bloc bloquant nécessitant un accompagnement prioritaire',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'code' => 'fragile',
                'titre' => 'Bloc fragile',
                'description' => 'Bloc insuffisamment structuré',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'code' => 'intermediaire',
                'titre' => 'Bloc intermédiaire',
                'description' => 'Bloc partiellement structuré',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'code' => 'conforme',
                'titre' => 'Bloc conforme',
                'description' => 'Bloc conforme aux attentes du palier',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'code' => 'reference',
                'titre' => 'Bloc de référence CJES',
                'description' => 'Bloc exemplaire – niveau référence',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
