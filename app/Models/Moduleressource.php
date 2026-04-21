<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moduleressource extends Model
{
    use HasFactory;

    protected $table = 'moduleressources';

    protected $appends = ['nom_complet'];

    /**
     * @var array
     */
    protected $fillable = [
        'montant',
        'reference',
        'description',
        'module_type',
        'module_id',
        'ressourcecompte_id',
        'prestation_id',
        'paiementstatut_id',
        'membre_id',
        'entreprise_id',
        'spotlight',
        'etat',
    ];
    
    /**
     * Le module_id peut pointer vers différentes tables (modules, cours, formations, etc.)
     * Cette relation est générique et doit être gérée selon le contexte
     */
    public function getModuleTypeAttribute(): string
    {
        return $this->attributes['module_type'] ?? 'generic';
    }

    /**
     * Obtenir le module selon le type de manière dynamique
     */
    public function getModuleDataAttribute()
    {
        $moduleType = $this->module_type;
        $moduleId = $this->module_id;

        if (!$moduleType || !$moduleId) {
            return null;
        }

        switch ($moduleType) {
            case 'modules':
                return \App\Models\Module::find($moduleId);
            case 'cours':
                return \App\Models\Cours::find($moduleId);
            case 'formations':
                return \App\Models\Formation::find($moduleId);
            case 'prestations':
                return \App\Models\Prestation::find($moduleId);
            case 'accompagnements':
                return \App\Models\Accompagnement::find($moduleId);
            case 'diagnostics':
                return \App\Models\Diagnostic::find($moduleId);
            default:
                // Type générique ou non reconnu
                return null;
        }
    }

    /**
     * Obtenir le titre du module selon le type
     */
    public function getModuleTitreAttribute(): string
    {
        $moduleData = $this->module_data;
        
        if ($moduleData && isset($moduleData->titre)) {
            return $moduleData->titre;
        }
        
        if ($moduleData && isset($moduleData->nom)) {
            return $moduleData->nom;
        }
        
        // Fallback sur la description ou ID générique
        return $this->description ?? "Module #{$this->module_id} ({$this->module_type})";
    }

    public function ressourcecompte()
    {
        return $this->belongsTo(Ressourcecompte::class);
    }

    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }

    public function paiementstatut()
    {
        return $this->belongsTo(Paiementstatut::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function getNomCompletAttribute(): string
    {
        $membre = $this->membre ? "{$this->membre->prenom} {$this->membre->nom}" : '';
        $entreprise = $this->entreprise ? $this->entreprise->nom : '';
        $module = $this->module_titre ?? $this->description ?? "Module #{$this->module_id}";
        return trim("Module #{$this->id} - $module - $membre - $entreprise");
    }

    /**
     * Obtenir le titre du module formaté
     */
    public function getTitreModuleAttribute(): string
    {
        return $this->module_titre ?? $this->description ?? "Module ID #{$this->module_id} ({$this->module_type})";
    }

    /**
     * Vérifier si le module est actif
     */
    public function estActif(): bool
    {
        return $this->etat == 1;
    }

    /**
     * Obtenir le montant formaté
     */
    public function getMontantFormatteAttribute(): string
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }
}
