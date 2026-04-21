<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experttype extends Model
{
    use HasFactory;

    protected $table = 'experttypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
