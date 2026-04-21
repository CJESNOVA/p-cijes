<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accompagnement;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $appends = ['nom_complet'];

    /**
     * @var array
     */
    protected $fillable = [
        'objectif',
        'actionprioritaire',
        'dateplan',
        'accompagnement_id',
        'plantemplate_id',
        'diagnosticmodule_id',
        'diagnosticquestion_id',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'accompagnement_id' => 'integer',
        'plantemplate_id' => 'integer',
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

    public function plantemplate()
    {
        return $this->belongsTo(Plantemplate::class);
    }

    public function diagnosticmodule()
    {
        return $this->belongsTo(Diagnosticmodule::class);
    }

    public function diagnosticquestion()
    {
        return $this->belongsTo(Diagnosticquestion::class);
    }

    public function getNomCompletAttribute()
    {
        $date = $this->dateplan ? $this->dateplan->format('d/m/Y') : 'Date non définie';
        $plantemplate = $this->plantemplate ? $this->plantemplate->diagnosticmodule->titre : 'Template inconnu';
        return "Plan: {$plantemplate} - {$date} - " . substr($this->objectif, 0, 30) . "...";
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
