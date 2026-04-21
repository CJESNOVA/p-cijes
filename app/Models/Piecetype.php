<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piecetype extends Model
{
    use HasFactory;

    protected $table = 'piecetypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
