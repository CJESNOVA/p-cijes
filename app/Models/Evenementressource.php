<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenementressource extends Model
{
    use HasFactory;

    protected $table = 'evenementressources';

    protected $appends = ['nom_complet'];


    /**
     * @var array
     */
    protected $fillable = [
        'montant',
        'reference',
        'accompagnement_id',
        'ressourcecompte_id',
        'evenement_id',
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

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
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
        return trim("Paiements des évènements #{$this->id} - $membre - $entreprise)");
    }


}
