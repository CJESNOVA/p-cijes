<?php

echo "=== TEST CONNEXION SMTP ===\n\n";

$host = 'mail.cjes.africa';
$port = 587;
$timeout = 5; // 5 secondes

echo "Test de connexion à {$host}:{$port}\n";
echo "Timeout: {$timeout} secondes\n\n";

try {
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    
    if ($socket) {
        echo "✅ Connexion réussie !\n";
        echo "Code erreur : {$errno}\n";
        echo "Message erreur : '{$errstr}'\n\n";
        
        // Lire la réponse du serveur SMTP
        stream_set_timeout($socket, 2);
        $response = fgets($socket, 1024);
        echo "Réponse serveur : " . trim($response) . "\n";
        
        fclose($socket);
        
        echo "\n🎯 Le serveur SMTP est accessible !\n";
        echo "Le problème vient probablement de la configuration Laravel.\n";
        
    } else {
        echo "❌ Connexion échouée\n";
        echo "Code erreur : {$errno}\n";
        echo "Message erreur : '{$errstr}'\n\n";
        
        echo "🔍 Analyse de l'erreur :\n";
        switch ($errno) {
            case 110:
                echo "- Timeout : le serveur ne répond pas\n";
                echo "- Vérifiez le firewall/VPN\n";
                break;
            case 111:
                echo "- Connection refused : le port 587 est fermé\n";
                echo "- Le serveur SMTP n'écoute peut-être pas sur ce port\n";
                break;
            case 113:
                echo "- No route to host : DNS ou réseau\n";
                echo "- Vérifiez la connexion internet\n";
                break;
            default:
                echo "- Erreur réseau inconnue\n";
        }
        
        echo "\n💡 Solutions :\n";
        echo "1. Utiliser MAIL_MAILER=log pour désactiver les emails\n";
        echo "2. Configurer un autre serveur SMTP\n";
        echo "3. Utiliser un service externe (SendGrid, Mailgun)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception : " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
