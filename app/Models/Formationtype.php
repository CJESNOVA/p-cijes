<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formationtype extends Model
{
    use HasFactory;

    protected $table = 'formationtypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
