<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participantstatut extends Model
{
    use HasFactory;

    protected $table = 'participantstatuts'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
