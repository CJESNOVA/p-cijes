<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reductiontype extends Model
{
    use HasFactory;

    protected $table = 'reductiontypes';

    protected $fillable = [
        'titre',
        'entrepriseprofil_id',
        'offretype_id',
        'pourcentage',
        'montant',
        'date_debut',
        'date_fin',
        'etat',
    ];

    protected $casts = [
        'pourcentage' => 'decimal:2',
        'montant' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'etat' => 'boolean',
    ];

    protected $appends = ['titre_complet'];

    public function entrepriseprofil()
    {
        return $this->belongsTo(Entrepriseprofil::class);
    }

    public function offretype()
    {
        return $this->belongsTo(Offretype::class);
    }

    public function getTitreCompletAttribute(): string
    {
        $profilTitre = $this->entrepriseprofil ? $this->entrepriseprofil->titre : '';
        $offreTitre = $this->offretype ? $this->offretype->titre : '';
        
        if ($profilTitre && $offreTitre) {
            return "{$profilTitre} - {$offreTitre}";
        } elseif ($profilTitre) {
            return $profilTitre;
        } elseif ($offreTitre) {
            return $offreTitre;
        }
        
        return $this->titre ?: 'Réduction sans titre';
    }

    public function isPromotionActive(): bool
    {
        if (!$this->date_debut || !$this->date_fin) {
            return false;
        }
        
        $now = now()->startOfDay();
        $dateDebut = $this->date_debut->startOfDay();
        $dateFin = $this->date_fin->startOfDay();
        
        return $now->gte($dateDebut) && $now->lte($dateFin);
    }

    public function getPromotionStatusAttribute(): string
    {
        if (!$this->date_debut || !$this->date_fin) {
            return 'Pas de promotion';
        }
        
        $now = now()->startOfDay();
        $dateDebut = $this->date_debut->startOfDay();
        $dateFin = $this->date_fin->startOfDay();
        
        if ($now->lt($dateDebut)) {
            return 'À venir';
        } elseif ($now->gt($dateFin)) {
            return 'Terminée';
        } else {
            return 'Active';
        }
    }

    public function scopeActive($query)
    {
        return $query->where('etat', true);
    }

    public function scopeActivePromotion($query)
    {
        return $query->whereNotNull('date_debut')
                    ->whereNotNull('date_fin')
                    ->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now())
                    ->where('etat', true);
    }

    public function scopeUpcomingPromotion($query)
    {
        return $query->whereNotNull('date_debut')
                    ->where('date_debut', '>', now())
                    ->where('etat', true);
    }

    public function scopeExpiredPromotion($query)
    {
        return $query->whereNotNull('date_fin')
                    ->where('date_fin', '<', now())
                    ->where('etat', true);
    }

    public function scopeForProfil($query, $profilId)
    {
        return $query->where('entrepriseprofil_id', $profilId);
    }

    public function scopeForOffre($query, $offreId)
    {
        return $query->where('offretype_id', $offreId);
    }

    public function calculateReduction($prixOriginal)
    {
        if ($this->pourcentage > 0) {
            return $prixOriginal * ($this->pourcentage / 100);
        } elseif ($this->montant > 0) {
            return $this->montant;
        }
        return 0;
    }

    public function getPrixAvecReduction($prixOriginal)
    {
        $reduction = $this->calculateReduction($prixOriginal);
        $prixFinal = $prixOriginal - $reduction;
        
        return max(0, $prixFinal); // Ne pas aller en dessous de 0
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($reduction) {
            // Soit pourcentage, soit montant, pas les deux
            if ($reduction->pourcentage > 0 && $reduction->montant > 0) {
                throw new \Exception('Une réduction ne peut avoir à la fois un pourcentage et un montant fixe');
            }
            
            // Date fin après date début
            if ($reduction->date_fin && $reduction->date_debut && $reduction->date_fin->lt($reduction->date_debut)) {
                throw new \Exception('La date de fin doit être après la date de début');
            }
            
            // Pourcentage entre 0 et 100
            if ($reduction->pourcentage < 0 || $reduction->pourcentage > 100) {
                throw new \Exception('Le pourcentage doit être entre 0 et 100');
            }
            
            // Montant positif
            if ($reduction->montant < 0) {
                throw new \Exception('Le montant de réduction doit être positif');
            }
        });
    }
}
