<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticquestiontype extends Model
{
    use HasFactory;

    protected $table = 'diagnosticquestiontypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
