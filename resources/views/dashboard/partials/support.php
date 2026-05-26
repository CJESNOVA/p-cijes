<?php

require '../vendor/autoload.php';

use Firebase\JWT\JWT;

// --- CONFIGURATION (Doit matcher notre système) ---
$secret_key = "e-cjes-support-secret-key-2024-very-long-key-for-hmac-sha256-encoding"; 
$plateforme_code = "CIJES"; // Utiliser une plateforme qui existe dans notre base
$callback_url = "http://localhost/support/public/sso/callback";

// --- DONNÉES DE L'UTILISATEUR FICTIF ---
$payload = [
    "user_id" => $membre->user_id, // ID externe dans le système source
    "nom" => $membre->nom,
    "prenom" => $membre->prenom,
    "email" => $membre->email,
    "plateforme_code" => $plateforme_code,
    "telephone" => $membre->telephone,
    "role" => "Demandeur", // Rôle dans le système source
    "iat" => time(), // Émis à
    "exp" => time() + (60 * 5) // Expire dans 5 minutes
];

// --- GÉNÉRATION DU TOKEN ---
$jwt = JWT::encode($payload, $secret_key, 'HS256');

// --- GÉNÉRATION DU LIEN DE TEST ---
$final_url = $callback_url . "?token=" . $jwt;

echo "<a href='{$final_url}' 
                       target='_blank' 
                       rel='noopener noreferrer'
                       class='inline-flex items-center px-6 py-3 bg-[#1DA8BB] text-white rounded-lg hover:bg-[#1DA8BB]/90 transition-colors shadow-lg'>
    Besoin de Support
</a>";
/*
$tks = explode('.', $final_url);
            $payload = json_decode(base64_decode($tks[1]));

dd($payload);*/