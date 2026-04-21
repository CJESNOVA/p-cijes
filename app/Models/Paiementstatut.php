<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiementstatut extends Model
{
    use HasFactory;

    protected $table = 'paiementstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
