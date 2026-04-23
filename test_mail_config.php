<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST CONFIGURATION MAIL ===\n\n";

echo "Configuration actuelle :\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "MAIL_USERNAME: " . (env('MAIL_USERNAME') ? 'défini' : 'non défini') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";

echo "\nTest de connexion SMTP...\n";

try {
    $transport = new \Swift_SmtpTransport(
        env('MAIL_HOST'),
        env('MAIL_PORT'),
        env('MAIL_ENCRYPTION')
    );
    
    echo "Transport créé avec succès\n";
    
    // Test avec timeout court
    $transport->setTimeout(5); // 5 secondes au lieu de 30
    
    $mailer = new \Swift_Mailer($transport);
    echo "Mailer créé\n";
    
    // Test simple
    $message = new \Swift_Message('Test', 'Contenu test', ['test@example.com']);
    $message->setFrom([env('MAIL_FROM_ADDRESS') => 'Test']);
    $message->setTo(['test@example.com']);
    
    echo "Tentative d'envoi (timeout 5s)...\n";
    $result = $mailer->send($message);
    
    echo "✅ Email envoyé avec succès !\n";
    echo "Nombre d'envois : " . $result . "\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur SMTP : " . $e->getMessage() . "\n";
    echo "Code : " . $e->getCode() . "\n";
    
    if (strpos($e->getMessage(), 'Connection timed out') !== false) {
        echo "\n💡 SOLUTION :\n";
        echo "1. Le serveur SMTP mail.cjes.africa:587 n'est pas accessible\n";
        echo "2. Vérifiez le firewall/VPN\n";
        echo "3. Utilisez MAIL_MAILER=log pour désactiver l'envoi\n";
        echo "4. Configurez un relais SMTP local\n";
    }
}

echo "\n=== RECOMMANDATION ===\n";
echo "Pour résoudre le problème de timeout :\n";
echo "1. Désactivez temporairement les emails : MAIL_MAILER=log\n";
echo "2. Ou augmentez le timeout dans config/mail.php\n";
echo "3. Ou utilisez un service SMTP externe (SendGrid, Mailgun...)\n";
