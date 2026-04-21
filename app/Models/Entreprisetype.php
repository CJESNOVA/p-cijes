<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprisetype extends Model
{
    use HasFactory;

    protected $table = 'entreprisetypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
