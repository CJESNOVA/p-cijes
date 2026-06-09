<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entreprise;

$total = Entreprise::count();
$missing = Entreprise::whereNull('supabase_startup_id')->orWhere('supabase_startup_id','')->count();
$examplesMissing = Entreprise::whereNull('supabase_startup_id')->orWhere('supabase_startup_id','')->limit(10)->get(['id','nom','supabase_startup_id'])->toArray();
$examplesPresent = Entreprise::whereNotNull('supabase_startup_id')->limit(10)->get(['id','nom','supabase_startup_id'])->toArray();

echo "total:" . $total . PHP_EOL;
echo "missing:" . $missing . PHP_EOL;
echo "examplesMissing:" . PHP_EOL . json_encode($examplesMissing, JSON_PRETTY_PRINT) . PHP_EOL;
echo "examplesPresent:" . PHP_EOL . json_encode($examplesPresent, JSON_PRETTY_PRINT) . PHP_EOL;
