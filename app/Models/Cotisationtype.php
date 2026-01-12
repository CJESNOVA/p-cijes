<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotisationtype extends Model
{
    use HasFactory;

    protected $table = 'cotisationtypes';

    protected $fillable = [
        'titre',
        'montant',
        'entrepriseprofil_id',
        'nombre_jours',
        'etat',
    ];

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function entrepriseprofil()
    {
        return $this->belongsTo(Entrepriseprofil::class);
    }
}
