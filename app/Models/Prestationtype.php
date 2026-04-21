<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestationtype extends Model
{
    use HasFactory;

    protected $table = 'prestationtypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
