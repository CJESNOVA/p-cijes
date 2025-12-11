<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conseillertype extends Model
{
    use HasFactory;

    protected $table = 'conseillertypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
