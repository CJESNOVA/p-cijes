<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membretype extends Model
{
    use HasFactory;

    protected $table = 'membretypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
