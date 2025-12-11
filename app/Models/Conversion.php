<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    use HasFactory;

    protected $table = 'conversions';

    /**
     * @var array
     */
    protected $fillable = [
        'taux',
        'ressourcetransaction_source_id',
        'ressourcetransaction_cible_id',
        'membre_id',
        'entreprise_id',
        'spotlight',
        'etat',
    ];
    
    public function ressourcetransactionsource()
    {
        return $this->belongsTo(Ressourcetransaction::class, 'ressourcetransaction_source_id');
    }
    
    public function ressourcetransactioncible()
    {
        return $this->belongsTo(Ressourcetransaction::class, 'ressourcetransaction_cible_id');
    }
    
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }
    
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }



}
