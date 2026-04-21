<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    use HasFactory;

    protected $table = 'cotisations';

    protected $fillable = [
        'entreprise_id',
        'cotisationtype_id',
        'montant',
        'montant_paye',
        'montant_restant',
        'devise',
        'date_debut',
        'date_fin',
        'date_echeance',
        'date_paiement',
        'statut',
        'est_a_jour',
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
        'est_a_jour' => 'boolean',
        'etat' => 'boolean',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function cotisationtype()
    {
        return $this->belongsTo(Cotisationtype::class);
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
}
