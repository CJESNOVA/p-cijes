<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class SupabaseUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        $user = session('supabase_user');
        return $user ? new \App\Auth\SupabaseUser($user) : null;
    }

    public function retrieveByToken($identifier, $token) { return null; }

    public function updateRememberToken(Authenticatable $user, $token) {}

    public function retrieveByCredentials(array $credentials) { return null; }

    public function validateCredentials(Authenticatable $user, array $credentials) { return true; }

    /**
     * Laravel 10.16+ : signature correcte
     */
    public function rehashPasswordIfRequired(
        Authenticatable $user,
        array $credentials,
        bool $force = false
    ): ?string {
        return null; // pas utilisé, on laisse Supabase gérer l'auth
    }
}
