<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espacetype extends Model
{
    use HasFactory;

    protected $table = 'espacetypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
