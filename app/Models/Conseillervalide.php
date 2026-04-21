<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conseillervalide extends Model
{
    use HasFactory;

    protected $table = 'conseillervalides'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
