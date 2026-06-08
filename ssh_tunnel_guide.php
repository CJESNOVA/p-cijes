<?php

echo "=== GUIDE SSH TUNNEL POUR BASE PRODUCTION ===\n\n";

echo "Si vous avez un accès SSH au serveur de production :\n\n";

echo "1. CRÉEZ UN TUNNEL SSH :\n";
echo "   ssh -L 3307:localhost:3306 utilisateur@serveur-production.com\n";
echo "   (Cela redirige le port 3306 distant vers le port 3307 local)\n\n";

echo "2. MODIFIEZ VOTRE .env :\n";
echo "   DB_HOST=127.0.0.1\n";
echo "   DB_PORT=3307\n";
echo "   DB_DATABASE=ecijes\n";
echo "   DB_USERNAME=ptchabao\n";
echo "   DB_PASSWORD=votre_mot_de_passe\n\n";

echo "3. TESTEZ LA CONNEXION :\n";
echo "   php artisan config:clear\n";
echo "   php check_current_env.php\n\n";

echo "4. POUR TESTER LES RÉCOMPENSES :\n";
echo "   php test_recompense_email.php\n\n";

echo "⚠️  AVANTAGES :\n";
echo "- Connexion sécurisée via SSH\n";
echo "- Pas besoin d'ouvrir le port MySQL publiquement\n";
echo "- Utilise les identifiants MySQL locaux\n\n";

echo "⚠️  INCONVÉNIENTS :\n";
echo "- Nécessite un accès SSH\n";
echo "- Le tunnel doit rester ouvert pendant les tests\n\n";

echo "📝 AUTRE SOLUTION :\n";
echo "Utilisez phpMyAdmin ou Adminer sur le serveur de production\n";
echo "pour vérifier directement les tables 'recompenses' et 'membres'\n";
