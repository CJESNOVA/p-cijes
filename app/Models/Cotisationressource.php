<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotisationressource extends Model
{
    use HasFactory;

    protected $table = 'cotisationressources';

    protected $fillable = [
        'montant',
        'reference',
        'accompagnement_id',
        'ressourcecompte_id',
        'cotisation_id',
        'paiementstatut_id',
        'membre_id',
        'entreprise_id',
        'spotlight',
        'etat',
    ];

    public function ressourcecompte()
    {
        return $this->belongsTo(Ressourcecompte::class);
    }

    public function cotisation()
    {
        return $this->belongsTo(Cotisation::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function paiementstatut()
    {
        return $this->belongsTo(Paiementstatut::class);
    }

    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }
}
