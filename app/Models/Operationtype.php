<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operationtype extends Model
{
    use HasFactory;

    protected $table = 'operationtypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
