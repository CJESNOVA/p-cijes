<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membrecategorie extends Model
{
    use HasFactory;

    protected $table = 'membrecategories';

    protected $fillable = [
        'titre',
        'etat',
    ];

    public function membretypes()
    {
        return $this->hasMany(Membretype::class);
    }
}
