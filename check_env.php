<?php

echo "=== VÉRIFICATION .env ===\n\n";

if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    echo "Contenu du .env :\n";
    echo $envContent . "\n\n";
    
    // Chercher SUPABASE_URL
    if (preg_match('/SUPABASE_URL=(.+)/', $envContent, $matches)) {
        echo "SUPABASE_URL trouvée: " . trim($matches[1]) . "\n";
    } else {
        echo "❌ SUPABASE_URL non trouvée\n";
    }
} else {
    echo "❌ Fichier .env non trouvé\n";
}

echo "\n=== FIN VÉRIFICATION ===\n";
?>
