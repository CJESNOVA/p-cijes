<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticstatutregle extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrepriseprofil_id',
        'score_total_min',
        'score_total_max',
        'min_blocs_score',
        'min_score_bloc',
        'bloc_juridique_min',
        'bloc_finance_min',
        'aucun_bloc_inf',
        'duree_min_mois',
    ];

    protected $casts = [
        'entrepriseprofil_id' => 'integer',
        'score_total_min' => 'integer',
        'score_total_max' => 'integer',
        'min_blocs_score' => 'integer',
        'min_score_bloc' => 'integer',
        'bloc_juridique_min' => 'integer',
        'bloc_finance_min' => 'integer',
        'aucun_bloc_inf' => 'integer',
        'duree_min_mois' => 'integer',
    ];


    /**
     * Relation avec le profil de l'entreprise
     */
    public function entrepriseprofil()
    {
        return $this->belongsTo(Entrepriseprofil::class);
    }

    /**
     * Vérifier si un score satisfait cette règle
     */
    public function verifierScore($scoreTotal, $blocsScores = [], $dureeMois = 0)
    {
        // Vérifier le score total
        if ($this->score_total_min !== null && $scoreTotal < $this->score_total_min) {
            return false;
        }
        if ($this->score_total_max !== null && $scoreTotal > $this->score_total_max) {
            return false;
        }

        // Vérifier la durée minimale
        if ($this->duree_min_mois > 0 && $dureeMois < $this->duree_min_mois) {
            return false;
        }

        // Vérifier les blocs de scores (logique globale)
        if (!empty($blocsScores)) {
            // Vérifier le nombre minimum de blocs avec score
            if ($this->min_blocs_score !== null) {
                $blocsAvecScore = collect($blocsScores)->filter(fn($score) => $score > 0)->count();
                if ($blocsAvecScore < $this->min_blocs_score) {
                    return false;
                }
            }

            // Vérifier le score minimum par bloc
            if ($this->min_score_bloc !== null) {
                $blocsInfMin = collect($blocsScores)->filter(fn($score) => $score < $this->min_score_bloc)->count();
                if ($blocsInfMin > 0) {
                    return false;
                }
            }

            // Vérifier les blocs spécifiques
            if ($this->bloc_juridique_min !== null && isset($blocsScores['JURIDIQUE']) && $blocsScores['JURIDIQUE'] < $this->bloc_juridique_min) {
                return false;
            }
            if ($this->bloc_finance_min !== null && isset($blocsScores['FINANCE']) && $blocsScores['FINANCE'] < $this->bloc_finance_min) {
                return false;
            }

            // Vérifier qu'aucun bloc n'est inférieur au seuil
            if ($this->aucun_bloc_inf !== null) {
                $blocsInfSeuil = collect($blocsScores)->filter(fn($score) => $score < $this->aucun_bloc_inf)->count();
                if ($blocsInfSeuil > 0) {
                    return false;
                }
            }
        }

        return true;
    }
}
