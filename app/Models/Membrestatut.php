<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membrestatut extends Model
{
    use HasFactory;

    protected $table = 'membrestatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
