<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditstatut extends Model
{
    use HasFactory;

    protected $table = 'creditstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
