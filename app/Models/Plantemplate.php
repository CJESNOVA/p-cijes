<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Diagnosticmodule;
use App\Models\Diagnosticquestion;

class Plantemplate extends Model
{
    use HasFactory;

    protected $table = 'plantemplates';

    protected $appends = ['nom_complet'];

    /**
     * @var array
     */
    protected $fillable = [
        'diagnosticmodule_id',
        'diagnosticquestion_id',
        'niveau',
        'objectif',
        'actionprioritaire',
        'priorite',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'diagnosticmodule_id' => 'integer',
        'diagnosticquestion_id' => 'integer',
        'priorite' => 'integer',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];

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
        $module = $this->diagnosticmodule ? $this->diagnosticmodule->titre : 'Module inconnu';
        $niveau = $this->niveau ? "Niveau {$this->niveau}" : 'Niveau non défini';
        $priorite = $this->priorite ? "Priorité {$this->priorite}" : 'Priorité non définie';
        return "Template: {$module} - {$niveau} - {$priorite}";
    }

    public function scopeActif($query)
    {
        return $query->where('etat', 1);
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
