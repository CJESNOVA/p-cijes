<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;

    protected $table = 'abonnements';

    protected $fillable = [
        'entreprise_id',
        'abonnementtype_id',
        'montant',
        'montant_paye',
        'montant_restant',
        'devise',
        'date_debut',
        'date_fin',
        'date_echeance',
        'date_paiement',
        'statut',
        'est_actif',
        'nombre_rappels',
        'reference_paiement',
        'mode_paiement',
        'commentaires',
        'etat',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
        'est_actif' => 'boolean',
        'etat' => 'boolean',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function abonnementtype()
    {
        return $this->belongsTo(Abonnementtype::class);
    }

    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'paye' => 'Payé',
            'partiel' => 'Partiel',
            'retard' => 'En retard',
            default => $this->statut,
        };
    }

    public function estExpiré(): bool
    {
        return $this->date_fin <= now();
    }

    public function estEnRetard(): bool
    {
        return $this->date_echeance < now() && $this->statut !== 'paye';
    }

    public function joursRestants(): int
    {
        return max(0, now()->diffInDays($this->date_fin));
    }

    public function peutEtreRenouvelé(): bool
    {
        return $this->estExpiré() && $this->statut === 'paye' && $this->etat;
    }

    public function ressourcesDisponibles()
    {
        $entreprise = $this->entreprise;
        $membreIds = $entreprise->entreprisesmembres()->pluck('membre_id')->toArray();
        
        return Ressourcecompte::where(function($query) use ($entreprise, $membreIds) {
                $query->where('entreprise_id', $entreprise->id)
                      ->orWhereIn('membre_id', $membreIds);
            })
            ->where('etat', 1)
            ->where('solde', '>=', $this->montant)
            ->orderBy('solde', 'desc')
            ->get();
    }
}
