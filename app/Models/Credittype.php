<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credittype extends Model
{
    use HasFactory;

    protected $table = 'credittypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
