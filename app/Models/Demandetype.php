<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demandetype extends Model
{
    use HasFactory;

    protected $table = 'demandetypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
