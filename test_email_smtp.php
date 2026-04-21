<?php

require_once __DIR__ . '/vendor/autoload.php';

// Forcer le chargement des variables d'environnement
\Dotenv\Dotenv::createImmutable(__DIR__)->load();

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\Membre;

echo "=== TEST EMAIL SMTP FORCÉ ===\n\n";

// Forcer la configuration
config(['mail.default' => 'smtp']);
config(['mail.mailers.smtp.host' => env('MAIL_HOST')]);
config(['mail.mailers.smtp.port' => env('MAIL_PORT')]);
config(['mail.mailers.smtp.username' => env('MAIL_USERNAME')]);
config(['mail.mailers.smtp.password' => env('MAIL_PASSWORD')]);
config(['mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION')]);

echo "Configuration forcée :\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";

// Récupérer un membre
$membre = Membre::first();
if (!$membre) {
    echo "❌ Aucun membre trouvé\n";
    exit;
}

echo "\nMembre : {$membre->prenom} {$membre->nom} ({$membre->email})\n\n";

// Test envoi email
try {
    Mail::send('emails.recompense', [
        'userName' => $membre->prenom . ' ' . $membre->nom,
        'actionTitre' => 'Test SMTP Production',
        'points' => 500,
        'lien' => 'https://cijes.cjes.africa/',
        'notifiable' => $membre,
    ], function ($message) use ($membre) {
        $message->to($membre->email)
                ->subject('🚀 Test SMTP Production - CJES Africa');
    });

    echo "✅ Email SMTP envoyé avec succès !\n";
    echo "📧 Vérifiez votre boîte mail : {$membre->email}\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur SMTP: " . $e->getMessage() . "\n";
    echo "Détails : " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN TEST ===\n";
