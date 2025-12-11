<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnementstatut extends Model
{
    use HasFactory;

    protected $table = 'accompagnementstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
