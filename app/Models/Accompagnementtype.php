<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnementtype extends Model
{
    use HasFactory;

    protected $table = 'accompagnementtypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
