<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticorientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnosticmodule_id',
        'diagnosticblocstatut_id',
        'seuil_max',
        'dispositif',
    ];

    protected $casts = [
        'seuil_max' => 'integer',
    ];

    /**
     * Relation avec le module de diagnostic
     */
    public function diagnosticmodule()
    {
        return $this->belongsTo(Diagnosticmodule::class);
    }

    /**
     * Relation avec le bloc de statut
     */
    public function diagnosticblocstatut()
    {
        return $this->belongsTo(Diagnosticblocstatut::class);
    }

    /**
     * Obtenir les orientations pour un module et un score donnés
     */
    public static function getOrientationsPourModule($moduleId, $score)
    {
        return self::where('diagnosticmodule_id', $moduleId)
            ->where('seuil_max', '>=', $score)
            ->with(['diagnosticblocstatut'])
            ->orderBy('seuil_max', 'asc')
            ->get();
    }

    /**
     * Obtenir le dispositif recommandé pour un score donné
     */
    public static function getDispositifRecommande($moduleId, $score)
    {
        $orientation = self::where('diagnosticmodule_id', $moduleId)
            ->where('seuil_max', '>=', $score)
            ->orderBy('seuil_max', 'asc')
            ->first();

        return $orientation ? $orientation->dispositif : null;
    }

    /**
     * Scope pour un module spécifique
     */
    public function scopePourModule($query, $moduleId)
    {
        return $query->where('diagnosticmodule_id', $moduleId);
    }

    /**
     * Scope pour un bloc spécifique
     */
    public function scopePourBloc($query, $blocId)
    {
        return $query->where('diagnosticblocstatut_id', $blocId);
    }
}
