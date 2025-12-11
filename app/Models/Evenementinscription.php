<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenementinscription extends Model
{
    use HasFactory;

    protected $table = 'evenementinscriptions';

    /**
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'evenement_id',
        'dateevenementinscription',
        'evenementinscriptiontype_id',
        'spotlight',
        'etat',
    ];
    
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }

    public function evenementinscriptiontype()
    {
        return $this->belongsTo(Evenementinscriptiontype::class);
    }
    
    
}
