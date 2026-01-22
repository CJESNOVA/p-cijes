<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propositionstatut extends Model
{
    use HasFactory;

    protected $table = 'propositionstatuts';

    protected $fillable = [
        'titre',
        'etat',
    ];

    protected $casts = [
        'etat' => 'boolean',
    ];

    public function propositions()
    {
        return $this->hasMany(Proposition::class, 'propositionstatut_id');
    }
}
