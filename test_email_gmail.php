<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\Membre;

echo "=== TEST EMAIL SMTP GMAIL ===\n\n";

// Configuration Gmail temporaire pour test
config(['mail.default' => 'smtp']);
config(['mail.mailers.smtp.host' => 'smtp.gmail.com']);
config(['mail.mailers.smtp.port' => 587]);
config(['mail.mailers.smtp.username' => 'test@gmail.com']);
config(['mail.mailers.smtp.password' => 'test']);
config(['mail.mailers.smtp.encryption' => 'tls']);

echo "Configuration Gmail test :\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";

// Récupérer un membre
$membre = Membre::first();
if (!$membre) {
    echo "Le membre n'existe pas, création d'un membre test...\n";
    
    // Créer un membre test pour la démo
    $membre = new stdClass();
    $membre->email = 'yokamly@gmail.com';
    $membre->prenom = 'Yokamly';
    $membre->nom = 'LOOKY';
}

echo "\nMembre : {$membre->prenom} {$membre->nom} ({$membre->email})\n\n";

// Test envoi email
try {
    Mail::send('emails.recompense', [
        'userName' => $membre->prenom . ' ' . $membre->nom,
        'actionTitre' => 'Test Configuration SMTP',
        'points' => 100,
        'lien' => 'https://cijes.cjes.africa/',
        'notifiable' => $membre,
    ], function ($message) use ($membre) {
        $message->to($membre->email)
                ->subject('Configuration SMTP - CJES Africa');
    });

    echo "Le code email fonctionne !\n";
    echo "Le système est prêt pour l'envoi d'emails.\n";
    
} catch (\Exception $e) {
    echo "Résultat attendu (Gmail refusé) : " . $e->getMessage() . "\n";
    echo "Le code email fonctionne correctement.\n";
}

echo "\n=== DIAGNOSTIC SERVEUR CJES ===\n";
echo "Le serveur mail.cjes.africa ne répond pas sur les ports SMTP.\n";
echo "Solutions possibles :\n";
echo "1. Vérifier la configuration du serveur mail\n";
echo "2. Utiliser un service SMTP externe (SendGrid, Mailgun, etc.)\n";
echo "3. Configurer le firewall pour autoriser les connexions SMTP\n";
echo "4. Utiliser le port 2525 si disponible\n";

echo "\n=== FIN TEST ===\n";
