<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticmodulescore extends Model
{
    use HasFactory;

    protected $table = 'diagnosticmodulescores';

    protected $fillable = [
        'diagnostic_id',
        'diagnosticmodule_id',
        'score_total',
        'score_max',
        'score_pourcentage',
        'niveau',
    ];

    protected $casts = [
        'score_total' => 'integer',
        'score_max' => 'integer',
        'score_pourcentage' => 'decimal:2',
    ];

    public function diagnostic()
    {
        return $this->belongsTo(Diagnostic::class);
    }

    public function diagnosticmodule()
    {
        return $this->belongsTo(Diagnosticmodule::class);
    }

    public function getScorePourcentageAttribute($value)
    {
        return $value ? round($value, 2) : null;
    }

    public function getNiveauLibelleAttribute()
    {
        $niveaux = [
            'debutant' => 'Débutant',
            'intermediaire' => 'Intermédiaire', 
            'avance' => 'Avancé',
            'expert' => 'Expert'
        ];

        return $niveaux[$this->niveau] ?? $this->niveau;
    }

    public function getPerformanceAttribute()
    {
        if (!$this->score_pourcentage) return null;

        if ($this->score_pourcentage >= 80) return 'Excellent';
        if ($this->score_pourcentage >= 60) return 'Bon';
        if ($this->score_pourcentage >= 40) return 'Moyen';
        if ($this->score_pourcentage >= 20) return 'Faible';
        return 'Très faible';
    }

    public function scopeByDiagnostic($query, $diagnosticId)
    {
        return $query->where('diagnostic_id', $diagnosticId);
    }

    public function scopeByModule($query, $moduleId)
    {
        return $query->where('diagnosticmodule_id', $moduleId);
    }

    public function scopeOrderByScore($query, $direction = 'desc')
    {
        return $query->orderBy('score_pourcentage', $direction);
    }
}
