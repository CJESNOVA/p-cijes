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
        'membrecategorie_id',
        'etat',
    ];

    public function membrecategorie()
    {
        return $this->belongsTo(Membrecategorie::class);
    }

    public function membres()
    {
        return $this->hasMany(Membre::class);
    }
}
