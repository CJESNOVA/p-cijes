<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnementconseiller extends Model
{
    use HasFactory;

    protected $table = 'accompagnementconseillers';

    /**
     * @var array
     */
    protected $fillable = [
        'observation',
        'accompagnementtype_id',
        'conseiller_id',
        'datedebut',
        'datefin',
        'montant',
        'accompagnement_id',
        'spotlight',
        'etat',
    ];
    
    public function accompagnementtype()
    {
        return $this->belongsTo(Accompagnementtype::class);
    }

    public function conseiller()
    {
        return $this->belongsTo(Conseiller::class);
    }

    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }

}
