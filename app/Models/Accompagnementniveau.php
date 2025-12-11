<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnementniveau extends Model
{
    use HasFactory;

    protected $table = 'accompagnementniveaus'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
