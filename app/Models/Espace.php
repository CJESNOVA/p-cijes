<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espace extends Model
{
    use HasFactory;

    protected $table = 'espaces';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'capacite',
        'resume',
        'prix',
        'description',
        'vignette',
        'espacetype_id',
        'pays_id',
        'spotlight',
        'etat',
    ];

    public function espacetype()
    {
        return $this->belongsTo(Espacetype::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'espace_id');
    }

    public function reservationsAVenir()
    {
        return $this->hasMany(Reservation::class, 'espace_id')
                    ->where('datefin', '>=', now()) // seulement à partir d'aujourd'hui
                    ->orderBy('datedebut', 'asc');
    }

}
