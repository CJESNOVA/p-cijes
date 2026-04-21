<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizquestiontype extends Model
{
    use HasFactory;

    protected $table = 'quizquestiontypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
