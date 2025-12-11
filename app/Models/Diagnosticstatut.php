<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticstatut extends Model
{
    use HasFactory;

    protected $table = 'diagnosticstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
