<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizresultatstatut extends Model
{
    use HasFactory;

    protected $table = 'quizresultatstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
