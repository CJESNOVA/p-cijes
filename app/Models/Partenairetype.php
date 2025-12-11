<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partenairetype extends Model
{
    use HasFactory;

    protected $table = 'partenairetypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
