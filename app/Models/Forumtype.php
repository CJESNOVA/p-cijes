<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forumtype extends Model
{
    use HasFactory;

    protected $table = 'forumtypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];
}
