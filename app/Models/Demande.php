<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $table = 'demandes';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'fichier',
        'demandetype_id',
        'datedemande',
        'ressourcecompte_id',
        'spotlight',
        'etat',
    ];

    public function demandetype()
    {
        return $this->belongsTo(Demandetype::class);
    }

    public function ressourcecompte()
    {
        return $this->belongsTo(Ressourcecompte::class);
    }

}
