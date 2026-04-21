<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnementressource extends Model
{
    use HasFactory;

    protected $table = 'abonnementressources';

    protected $fillable = [
        'montant',
        'reference',
        'accompagnement_id',
        'ressourcecompte_id',
        'abonnement_id',
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

    public function abonnement()
    {
        return $this->belongsTo(Abonnement::class);
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
