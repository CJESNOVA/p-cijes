<?php

echo "=== VÉRIFICATION CONFIGURATION MAIL ===\n\n";

// Charger l'environnement Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Vérification du fichier .env...\n";
if (file_exists(__DIR__ . '/.env')) {
    echo "✅ Fichier .env trouvé\n";
    
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (strpos($envContent, 'MAIL_MAILER=smtp') !== false) {
        echo "✅ MAIL_MAILER=smtp trouvé dans .env\n";
    } else {
        echo "❌ MAIL_MAILER=smtp NON trouvé dans .env\n";
    }
} else {
    echo "❌ Fichier .env NON trouvé\n";
}

echo "\nConfiguration Laravel chargée :\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER', 'NON DÉFINI') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', 'NON DÉFINI') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT', 'NON DÉFINI') . "\n";
echo "MAIL_USERNAME: " . (env('MAIL_USERNAME') ? 'DÉFINI' : 'NON DÉFINI') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'NON DÉFINI') . "\n";

echo "\nConfiguration de config/mail.php :\n";
echo "default: " . config('mail.default') . "\n";
echo "from.address: " . config('mail.from.address') . "\n";
echo "from.name: " . config('mail.from.name') . "\n";

echo "\n=== FIN VÉRIFICATION ===\n";
