<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;

    protected $table = 'experts';

    /**
     * @var array
     */
    protected $fillable = [
        'domaine',
        'expertvalide_id',
        'fichier',
        'experttype_id',
        'membre_id',
        'secteur_id',
        'spotlight',
        'etat',
    ];
    
    public function expertvalide()
    {
        return $this->belongsTo(Expertvalide::class);
    }

    public function experttype()
    {
        return $this->belongsTo(Experttype::class);
    }

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getNoteMoyenneAttribute()
    {
        if ($this->evaluations->count() === 0) {
            return 0;
        }
        return round($this->evaluations->avg('note'), 1); // exemple : 4.3
    }

}
