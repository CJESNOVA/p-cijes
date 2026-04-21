<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bontype extends Model
{
    use HasFactory;

    protected $table = 'bontypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
