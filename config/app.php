<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value est le nom de ton application. Il sera utilisé dans les
    | notifications, titres, ou tout autre endroit où le nom de ton appli
    | doit apparaître.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | Définit l'environnement d'exécution : "local", "staging", "production", etc.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | Quand activé, Laravel affiche les détails complets des erreurs.
    | À désactiver en production.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | URL principale de ton application, utilisée notamment par les commandes Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Fuseau horaire par défaut utilisé par PHP et Laravel.
    | Mets "Africa/Lome" pour ton cas.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Africa/Lome'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Détermine la langue par défaut utilisée par Laravel.
    |
    */

    'locale' => env('APP_LOCALE', 'fr'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'fr_FR'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Clé de chiffrement utilisée par Laravel. Doit être une chaîne aléatoire
    | de 32 caractères.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => array_filter(
        explode(',', env('APP_PREVIOUS_KEYS', ''))
    ),

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | Détermine le driver utilisé pour le mode maintenance.
    | Si tu veux centraliser le mode maintenance, utilise "cache".
    | Sinon, garde "file" par défaut.
    |
    | Drivers supportés : "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),

        // Ce champ n’est utile que si le driver = cache :
        'store' => env('APP_MAINTENANCE_DRIVER') === 'cache'
            ? env('APP_MAINTENANCE_STORE', 'database')
            : null,
    ],

];
