<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accompagnement;

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

    protected $casts = [
        'dateplan' => 'date',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];

    protected $dates = [
        'dateplan',
    ];

    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('etat', true);
    }

    public function scopeSpotlight($query)
    {
        return $query->where('spotlight', true);
    }

    public function scopeByDate($query, $order = 'desc')
    {
        return $query->orderBy('dateplan', $order);
    }


    public function propositions()
    {
        return $this->hasMany(Proposition::class);
    }

}
