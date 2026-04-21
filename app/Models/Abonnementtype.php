<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnementtype extends Model
{
    use HasFactory;

    protected $table = 'abonnementtypes';

    protected $fillable = [
        'titre',
        'montant',
        'entrepriseprofil_id',
        'nombre_jours',
        'etat',
    ];

    public function abonnements()
    {
        return $this->hasMany(Abonnement::class);
    }

    public function entrepriseprofil()
    {
        return $this->belongsTo(Entrepriseprofil::class);
    }
}
