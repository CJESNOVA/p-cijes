<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressourcecompte extends Model
{
    use HasFactory;

    protected $table = 'ressourcecomptes';

    protected $appends = ['nom_complet'];

    /**
     * @var array
     */
    protected $fillable = [
        'solde',
        'membre_id',
        'ressourcetype_id',
        'entreprise_id',
        'spotlight',
        'etat',
        'resource_account_id',
    ];
    
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function ressourcetype()
    {
        return $this->belongsTo(Ressourcetype::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function getNomCompletAttribute(): string
    {
        $membre = $this->membre ? "{$this->membre->prenom} {$this->membre->nom}" : '';
        $entreprise = $this->entreprise ? $this->entreprise->nom : '';
        $ressourcetype = $this->ressourcetype ? $this->ressourcetype->titre : '';
        $solde = number_format($this->solde, 2);
        return trim("$ressourcetype - $solde");//$membre - $entreprise - 
    }

    public function ressourcetransactions()
    {
        return $this->hasMany(Ressourcetransaction::class, 'ressourcecompte_id');
    }

}
