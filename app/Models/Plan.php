<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    /**
     * @var array
     */
    protected $fillable = [
        'objectif',
        'actionprioritaire',
        'dateplan',
        'accompagnement_id',
        'spotlight',
        'etat',
    ];

    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }

}
