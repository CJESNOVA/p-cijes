<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\Membre;

echo "=== TEST EMAIL DIRECT ===\n\n";

// Récupérer un membre
$membre = Membre::first();
if (!$membre) {
    echo "❌ Aucun membre trouvé\n";
    exit;
}

echo "Membre : {$membre->prenom} {$membre->nom} ({$membre->email})\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n\n";

// Test envoi email direct
try {
    Mail::send('emails.recompense', [
        'userName' => $membre->prenom . ' ' . $membre->nom,
        'actionTitre' => 'Test Direct',
        'points' => 100,
        'lien' => 'https://cijes.cjes.africa/',
        'notifiable' => $membre,
    ], function ($message) use ($membre) {
        $message->to($membre->email)
                ->subject('🧪 Test Email Direct - CJES Africa');
    });

    echo "✅ Email envoyé avec succès !\n";
    
    // Vérifier les logs
    if (env('MAIL_MAILER') === 'log') {
        echo "📝 Note: Les emails sont loggés (MAIL_MAILER=log)\n";
        echo "Pour envoyer réellement: changez MAIL_MAILER=smtp dans .env\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST ===\n";
