<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticmoduletype extends Model
{
    use HasFactory;

    protected $table = 'diagnosticmoduletypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
