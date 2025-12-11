<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestationrealiseestatut extends Model
{
    use HasFactory;

    protected $table = 'prestationrealiseestatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
