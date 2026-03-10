<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membre extends Model
{
    use HasFactory;

    protected $table = 'membres';

    /**
     * @var array
     */
    protected $fillable = [
        'numero_identifiant',
        'nom',
        'prenom',
        'email',
        'membrestatut_id',
        'vignette',
        'membretype_id',
        'user_id',
        'pays_id',
        'telephone',
        'etat',
    ];
    
    public function membrestatut()
    {
        return $this->belongsTo(Membrestatut::class);
    }

    public function membretype()
    {
        return $this->belongsTo(Membretype::class);
    }

    public function membrecategorie()
    {
        return $this->hasOneThrough(Membrecategorie::class, Membretype::class, 'id', 'id', 'membretype_id', 'membrecategorie_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function entreprisemembres()
{
    return $this->hasMany(Entreprisemembre::class, 'membre_id');
}

public function entreprises()
{
    return $this->belongsToMany(Entreprise::class, 'entreprisemembres', 'membre_id', 'entreprise_id');
}

/**
     * Génère un numéro d'identifiant unique
     */
    public static function generateNumeroIdentifiant()
    {
        $prefixe = 'MBR-CJESTG';
        $annee = date('y'); // Deux derniers chiffres de l'année
        $mois = date('m'); // Deux derniers chiffres du mois
        
        do {
            // Récupérer le dernier numéro d'ordre pour ce mois
            $dernierNumero = self::where('numero_identifiant', 'like', $prefixe . $annee . $mois . '%')
                ->orderBy('numero_identifiant', 'desc')
                ->first();
            
            if ($dernierNumero) {
                // Extraire le numéro d'ordre et l'incrémenter
                $dernierOrdre = substr($dernierNumero->numero_identifiant, -5);
                $nouvelOrdre = str_pad((int)$dernierOrdre + 1, 5, '0', STR_PAD_LEFT);
            } else {
                // Premier numéro du mois
                $nouvelOrdre = '00001';
            }
            
            $numero = $prefixe . $annee . $mois . $nouvelOrdre;
            
        } while (self::where('numero_identifiant', $numero)->exists());
        
        return $numero;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($membre) {
            if (empty($membre->numero_identifiant)) {
                $membre->numero_identifiant = self::generateNumeroIdentifiant();
            }
        });
    }

}
