<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticmodule extends Model
{
    use HasFactory;

    protected $table = 'diagnosticmodules';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'position',
        'description',
        'diagnosticmoduletype_id',
        'parent',
        'langue_id',
        'pays_id',
        'spotlight',
        'etat',
    ];
    

    public function diagnosticmoduletype()
    {
        return $this->belongsTo(Diagnosticmoduletype::class);
    }

    public function langue()
    {
        return $this->belongsTo(Langue::class);
    }

    public function moduleparent()
    {
        return $this->belongsTo(Diagnosticmodule::class, 'parent');
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function diagnosticquestions()
    {
        return $this->hasMany(Diagnosticquestion::class, 'diagnosticmodule_id');
    }

// Dans Diagnosticmodule.php
public function diagnosticresultats()
{
    return $this->hasManyThrough(
        Diagnosticresultat::class,   // Table finale
        Diagnosticquestion::class,   // Table intermédiaire
        'diagnosticmodule_id',       // clé étrangère sur Diagnosticquestion
        'diagnosticquestion_id',     // clé étrangère sur Diagnosticresultat
        'id',                        // clé locale sur Diagnosticmodule
        'id'                         // clé locale sur Diagnosticquestion
    );
}

/**
 * Récupérer les résultats filtrés pour un membre spécifique
 */
public function diagnosticresultatsPourMembre($membreId)
{
    return $this->hasManyThrough(
        Diagnosticresultat::class,
        Diagnosticquestion::class,
        'diagnosticmodule_id', // clé étrangère sur Diagnosticquestion
        'diagnosticquestion_id', // clé étrangère sur Diagnosticresultat
        'id', // clé locale Diagnosticmodule
        'id'  // clé locale Diagnosticquestion
    )->whereHas('diagnostic', function ($q) use ($membreId) {
        $q->where('membre_id', $membreId);
    });
}



}
