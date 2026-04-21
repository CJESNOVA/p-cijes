<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Diagnostic;
use App\Models\Diagnosticmodule;
use App\Models\Diagnosticquestion;
use App\Models\Diagnosticblocstatut;

class Diagnosticmodulescore extends Model
{
    use HasFactory;

    protected $table = 'diagnosticmodulescores';

    protected $appends = ['nom_complet'];

    /**
     * @var array
     */
    protected $fillable = [
        'diagnostic_id',
        'diagnosticmodule_id',
        'diagnosticquestion_id',
        'score_total',
        'score_max',
        'score_pourcentage',
        'diagnosticblocstatut_id',
    ];

    protected $casts = [
        'diagnostic_id' => 'integer',
        'diagnosticmodule_id' => 'integer',
        'diagnosticquestion_id' => 'integer',
        'score_total' => 'integer',
        'score_max' => 'integer',
        'score_pourcentage' => 'decimal:2',
        'diagnosticblocstatut_id' => 'integer',
    ];

    public function diagnostic()
    {
        return $this->belongsTo(Diagnostic::class);
    }

    public function diagnosticmodule()
    {
        return $this->belongsTo(Diagnosticmodule::class);
    }

    public function diagnosticquestion()
    {
        return $this->belongsTo(Diagnosticquestion::class);
    }

    public function diagnosticblocstatut()
    {
        return $this->belongsTo(Diagnosticblocstatut::class);
    }

    public function getNomCompletAttribute()
    {
        $module = $this->diagnosticmodule ? $this->diagnosticmodule->titre : 'Module inconnu';
        $diagnostic = $this->diagnostic ? "#{$this->diagnostic->id}" : 'Diagnostic inconnu';
        return "Score Module: {$module} - {$diagnostic} ({$this->score_pourcentage}%)";
    }

    public function getScorePourcentageAttribute($value)
    {
        return $value ? round($value, 2) : null;
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
