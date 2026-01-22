<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposition extends Model
{
    use HasFactory;

    protected $table = 'propositions';

    protected $fillable = [
        'membre_id',
        'expert_id',
        'prestation_id',
        'plan_id',
        'accompagnement_id',
        'message',
        'prix_propose',
        'duree_prevue',
        'propositionstatut_id',
        'date_proposition',
        'date_expiration',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'prix_propose' => 'decimal:2',
        'date_proposition' => 'datetime',
        'date_expiration' => 'datetime',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];

    protected $dates = [
        'date_proposition',
        'date_expiration',
    ];

    // Relations principales
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }

    // Relations contextuelles
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }

    // Statut
    public function statut()
    {
        return $this->belongsTo(Propositionstatut::class, 'propositionstatut_id');
    }

    // Scopes utiles
    public function scopeActive($query)
    {
        return $query->where('etat', true);
    }

    public function scopeSpotlight($query)
    {
        return $query->where('spotlight', true);
    }

    public function scopeEnAttente($query)
    {
        return $query->whereHas('statut', function($q) {
            $q->where('titre', 'En attente');
        });
    }

    public function scopeAcceptees($query)
    {
        return $query->whereHas('statut', function($q) {
            $q->where('titre', 'Acceptée');
        });
    }

    public function scopeRefusees($query)
    {
        return $query->whereHas('statut', function($q) {
            $q->where('titre', 'Refusée');
        });
    }

    // Accesseurs
    public function getDatePropositionFormateeAttribute()
    {
        return $this->date_proposition ? $this->date_proposition->format('d/m/Y H:i') : null;
    }

    public function getDateExpirationFormateeAttribute()
    {
        return $this->date_expiration ? $this->date_expiration->format('d/m/Y H:i') : null;
    }

    public function getPrixProposeFormateAttribute()
    {
        return $this->prix_propose ? number_format($this->prix_propose, 0, ',', ' ') . ' F CFA' : null;
    }

    public function isExpired()
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function isEnAttente()
    {
        return $this->statut && $this->statut->titre === 'En attente';
    }

    public function isAcceptee()
    {
        return $this->statut && $this->statut->titre === 'Acceptée';
    }

    public function isRefusee()
    {
        return $this->statut && $this->statut->titre === 'Refusée';
    }
}
