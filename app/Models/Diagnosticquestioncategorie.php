<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticquestioncategorie extends Model
{
    use HasFactory;

    protected $table = 'diagnosticquestioncategories'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
