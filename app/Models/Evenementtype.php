<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenementtype extends Model
{
    use HasFactory;

    protected $table = 'evenementtypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
