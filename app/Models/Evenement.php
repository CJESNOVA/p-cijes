<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $table = 'evenements';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'resume',
        'prix',
        'description',
        'langue_id',
        'vignette',
        'evenementtype_id',
        'dateevenement',
        'pays_id',
        'spotlight',
        'etat',
    ];
    
    public function langue()
    {
        return $this->belongsTo(Langue::class);
    }

    public function evenementtype()
    {
        return $this->belongsTo(Evenementtype::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Evenementinscription::class);
    }

}
