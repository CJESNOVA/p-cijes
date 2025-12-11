<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostictype extends Model
{
    use HasFactory;

    protected $table = 'diagnostictypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
