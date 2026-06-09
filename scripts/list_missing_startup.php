<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entreprise;

$rows = Entreprise::whereNull('supabase_startup_id')
    ->orWhere('supabase_startup_id', '')
    ->limit(100)
    ->get(['id', 'nom', 'supabase_startup_id'])
    ->toArray();

echo json_encode($rows, JSON_PRETTY_PRINT);
