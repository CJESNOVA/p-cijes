<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expertvalide extends Model
{
    use HasFactory;

    protected $table = 'expertvalides'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
