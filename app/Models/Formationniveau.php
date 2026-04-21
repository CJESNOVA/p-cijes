<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formationniveau extends Model
{
    use HasFactory;

    protected $table = 'formationniveaus'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
