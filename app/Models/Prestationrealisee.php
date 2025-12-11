<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestationrealisee extends Model
{
    use HasFactory;

    protected $table = 'prestationrealisees';

    /**
     * @var array
     */
    protected $fillable = [
        'note',
        'feedback',
        'prestation_id',
        'accompagnement_id',
        'daterealisation',
        'prestationrealiseestatut_id',
        'spotlight',
        'etat',
    ];
    
    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }

    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }

    public function prestationrealiseestatut()
    {
        return $this->belongsTo(Prestationrealiseestatut::class);
    }


}
