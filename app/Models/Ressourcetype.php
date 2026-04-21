<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressourcetype extends Model
{
    use HasFactory;

    protected $table = 'ressourcetypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];

    
    public function ressourcecomptes()
    {
        return $this->hasMany(Ressourcecompte::class, 'ressourcetype_id');
    }
    
}
