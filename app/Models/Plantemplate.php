<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantemplate extends Model
{
    use HasFactory;

    protected $table = 'plantemplates';

    protected $fillable = [
        'diagnosticmodule_id',
        'niveau',
        'objectif',
        'actionprioritaire',
        'priorite',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'priorite' => 'integer',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];

    public function diagnosticmodule()
    {
        return $this->belongsTo(Diagnosticmodule::class);
    }

    public function scopeActif($query)
    {
        return $query->where('etat', true);
    }

    public function scopeSpotlight($query)
    {
        return $query->where('spotlight', true);
    }

    public function scopeByPriorite($query)
    {
        return $query->orderBy('priorite', 'asc');
    }

    public function scopeByNiveau($query, $niveau)
    {
        return $query->where('niveau', $niveau);
    }
}
