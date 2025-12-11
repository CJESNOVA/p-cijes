<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonstatut extends Model
{
    use HasFactory;

    protected $table = 'bonstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
