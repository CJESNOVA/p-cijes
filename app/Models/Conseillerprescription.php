<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conseillerprescription extends Model
{
    use HasFactory;

    protected $table = 'conseillerprescriptions';

    /**
     * @var array
     */
    protected $fillable = [
        'conseiller_id',
        'membre_id',
        'entreprise_id',
        'prestation_id',
        'formation_id',
        'spotlight',
        'etat',
    ];
    
    /*public function conseiller()
    {
        return $this->belongsTo(Conseiller::class);
    }*/
    public function conseiller()
    {
        return $this->belongsTo(Conseiller::class, 'conseiller_id')->withDefault();
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class, 'formation_id');
    }

    public function prestation()
    {
        return $this->belongsTo(Prestation::class, 'prestation_id');
    }

}
