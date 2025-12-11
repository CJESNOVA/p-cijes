<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    /**
     * @var array
     */
    protected $fillable = [
        'observation',
        'reservationstatut_id',
        'espace_id',
        'datedebut',
        'datefin',
        'membre_id',
        'spotlight',
        'etat',
    ];
    
    public function reservationstatut()
    {
        return $this->belongsTo(Reservationstatut::class);
    }

    public function espace()
    {
        return $this->belongsTo(Espace::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }
}
