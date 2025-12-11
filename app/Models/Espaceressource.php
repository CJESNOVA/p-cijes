<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espaceressource extends Model
{
    use HasFactory;

    protected $table = 'espaceressources';

    protected $appends = ['nom_complet'];


    /**
     * @var array
     */
    protected $fillable = [
        'montant',
        'reference',
        'accompagnement_id',
        'ressourcecompte_id',
        'espace_id',
        'paiementstatut_id',
        'membre_id',
        'entreprise_id',
        'spotlight',
        'etat',
    ];
    
    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }

    public function ressourcecompte()
    {
        return $this->belongsTo(Ressourcecompte::class);
    }

    public function espace()
    {
        return $this->belongsTo(Espace::class);
    }

    public function paiementstatut()
    {
        return $this->belongsTo(Paiementstatut::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function getNomCompletAttribute(): string
    {
        $membre = $this->membre ? "{$this->membre->prenom} {$this->membre->nom}" : '';
        $entreprise = $this->entreprise ? $this->entreprise->nom : '';
        return trim("Paiements des espaces #{$this->id} - $membre - $entreprise)");
    }


}
