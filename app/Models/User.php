<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'supabase_user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function membre()
    {
        return $this->hasOne(Membre::class); // ou belongsTo selon ton schéma
    }
    
    public function entreprises(): Collection
    {
        $entreprises = $this->membre ? $this->membre->entreprises : collect();
        
        // Ajouter une méthode exists() pour compatibilité
        if ($entreprises instanceof Collection) {
            $entreprises->macro('exists', function() {
                return $this->isNotEmpty();
            });
        }
        
        return $entreprises;
    }
    
    // Alternative si vous voulez vraiment utiliser hasManyThrough
    public function entreprisesDirect(): Collection
    {
        return $this->hasManyThrough(
            Entreprise::class,
            Membre::class,
            'user_id',
            'id',
            'id',
            'id'
        );
    }
    
    public function entreprisesViaMembre(): Collection
    {
        $entreprises = $this->membre ? $this->membre->entreprises : collect();
        
        // Ajouter une méthode exists() pour compatibilité
        if ($entreprises instanceof Collection) {
            $entreprises->macro('exists', function() {
                return $this->isNotEmpty();
            });
        }
        
        return $entreprises;
    }
    
    // Méthode pour vérifier si l'utilisateur a des entreprises
    public function hasEntreprises(): bool
    {
        return $this->entreprises->isNotEmpty();
    }
    
    // Méthode exists() directe pour compatibilité totale
    public function entreprisesExist(): bool
    {
        return $this->hasEntreprises();
    }
}
