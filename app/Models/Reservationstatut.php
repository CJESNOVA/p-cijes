<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservationstatut extends Model
{
    use HasFactory;

    protected $table = 'reservationstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
