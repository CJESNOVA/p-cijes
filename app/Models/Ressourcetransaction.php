<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressourcetransaction extends Model
{
    use HasFactory;

    protected $table = 'ressourcetransactions';

    /**
     * @var array
     */
    protected $fillable = [
        'montant',
        'reference',
        'ressourcecompte_id',
        'datetransaction',
        'operationtype_id',
        'description',
        'spotlight',
        'etat',
    ];
    
    public function ressourcecompte()
    {
        return $this->belongsTo(Ressourcecompte::class, 'ressourcecompte_id');
    }

    public function operationtype()
    {
        return $this->belongsTo(Operationtype::class);
    }

    // 🔗 Lien avec les ressources payées
    public function formationRessource()
    {
        return $this->hasOne(Formationressource::class, 'reference', 'reference');
    }

    public function prestationRessource()
    {
        return $this->hasOne(Prestationressource::class, 'reference', 'reference');
    }

    public function evenementRessource()
    {
        return $this->hasOne(Evenementressource::class, 'reference', 'reference');
    }

    public function espaceRessource()
    {
        return $this->hasOne(Espaceressource::class, 'reference', 'reference');
    }

    public function cotisationRessource()
    {
        return $this->hasOne(Cotisationressource::class, 'reference', 'reference');
    }

    // Méthode pratique pour obtenir l'origine (formation, prestation, événement, espace, cotisation)
    public function origine()
    {
        if ($this->formationRessource) return $this->formationRessource;
        if ($this->prestationRessource) return $this->prestationRessource;
        if ($this->evenementRessource) return $this->evenementRessource;
        if ($this->espaceRessource) return $this->espaceRessource;
        if ($this->cotisationRessource) return $this->cotisationRessource;

        return null;
    }

}
