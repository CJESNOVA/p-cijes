<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    use HasFactory;

    protected $table = 'alertes';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'contenu',
        'lienurl',
        'langue_id',
        'alertetype_id',
        'recompense_id',
        'datealerte',
        'membre_id',
        'lu',
        'etat',
    ];
    
    public function langue()
    {
        return $this->belongsTo(Langue::class);
    }

    public function alertetype()
    {
        return $this->belongsTo(Alertetype::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function recompense()
    {
        return $this->belongsTo(Recompense::class);
    }

}
