<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenementinscriptiontype extends Model
{
    use HasFactory;

    protected $table = 'evenementinscriptiontypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
