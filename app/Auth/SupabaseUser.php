<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class SupabaseUser implements Authenticatable
{
    protected array $user;

    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /** Retourne le nom de l'identifiant (email ou id) */
    public function getAuthIdentifierName(): string
    {
        return 'email';
    }

    /** Retourne l'identifiant de l'utilisateur */
    public function getAuthIdentifier(): string
    {
        return $this->user['email'] ?? $this->user['id'];
    }

    /** Pas utilisé car authentification via Supabase */
    public function getAuthPassword(): ?string
    {
        return null;
    }

    /** Nom du champ mot de passe (exigé par l'interface) */
    public function getAuthPasswordName(): ?string
    {
        return null;
    }

    public function getRememberToken(): ?string
    {
        return $this->user['id']; // n’importe quelle valeur unique
    }

    public function setRememberToken($value): void
    {
        // pas nécessaire
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }


    /** Retourne le tableau original de l'utilisateur */
    public function getUser(): array
    {
        return $this->user;
    }
}
