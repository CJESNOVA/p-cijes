<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entreprise;
use App\Models\Diagnostic;
use App\Models\Diagnosticstatut;
use App\Models\Entrepriseprofil;
use App\Models\Membre;

class Diagnosticevolution extends Model
{
    use HasFactory;

    protected $table = 'diagnosticevolutions';

    protected $appends = ['nom_complet'];

    protected $fillable = [
        'membre_id',
        'entreprise_id',
        'diagnostic_id',
        'diagnostic_precedent_id',
        'score_global',
        'diagnosticstatut_id',
        'entrepriseprofil_id',
        'commentaire',
    ];

    protected $casts = [
        'membre_id' => 'integer',
        'entreprise_id' => 'integer',
        'diagnostic_id' => 'integer',
        'diagnostic_precedent_id' => 'integer',
        'score_global' => 'integer',
        'diagnosticstatut_id' => 'integer',
        'entrepriseprofil_id' => 'integer',
    ];

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function diagnostic()
    {
        return $this->belongsTo(Diagnostic::class);
    }

    public function diagnosticPrecedent()
    {
        return $this->belongsTo(Diagnostic::class, 'diagnostic_precedent_id');
    }

    public function diagnosticstatut()
    {
        return $this->belongsTo(Diagnosticstatut::class);
    }

    public function entrepriseprofil()
    {
        return $this->belongsTo(Entrepriseprofil::class);
    }

    public function getNomCompletAttribute()
    {
        if ($this->entreprise) {
            return "Évolution #{$this->id} - " . $this->entreprise->nom . " (Score: {$this->score_global})";
        } elseif ($this->membre) {
            return "Évolution #{$this->id} - " . $this->membre->nom_complet . " (Score: {$this->score_global})";
        }
        return "Évolution #{$this->id} - Inconnue (Score: {$this->score_global})";
    }

    /**
     * Obtenir l'évolution du score par rapport au diagnostic précédent
     */
    public function getEvolutionScore()
    {
        if (!$this->diagnostic_precedent_id) {
            return null;
        }

        $scorePrecedent = $this->diagnosticPrecedent->scoreglobal ?? 0;
        return $this->score_global - $scorePrecedent;
    }

    /**
     * Obtenir le pourcentage d'évolution
     */
    public function getEvolutionPourcentage()
    {
        if (!$this->diagnostic_precedent_id) {
            return null;
        }

        $scorePrecedent = $this->diagnosticPrecedent->scoreglobal ?? 0;
        if ($scorePrecedent == 0) {
            return $this->score_global > 0 ? 100 : 0;
        }

        return round((($this->score_global - $scorePrecedent) / $scorePrecedent) * 100, 2);
    }

    /**
     * Vérifier si c'est une progression
     */
    public function estProgression()
    {
        $evolution = $this->getEvolutionScore();
        return $evolution !== null && $evolution > 0;
    }

    /**
     * Vérifier si c'est une régression
     */
    public function estRegression()
    {
        $evolution = $this->getEvolutionScore();
        return $evolution !== null && $evolution < 0;
    }

    /**
     * Vérifier si c'est stable
     */
    public function estStable()
    {
        $evolution = $this->getEvolutionScore();
        return $evolution !== null && $evolution == 0;
    }

    /**
     * Obtenir la couleur de l'évolution
     */
    public function getCouleurEvolution()
    {
        if ($this->estProgression()) {
            return 'green';
        } elseif ($this->estRegression()) {
            return 'red';
        } elseif ($this->estStable()) {
            return 'gray';
        }
        return 'blue';
    }

    /**
     * Obtenir les évolutions pour une entreprise
     */
    public static function pourEntreprise($entrepriseId, $limit = 10)
    {
        return self::where('entreprise_id', $entrepriseId)
            ->with(['diagnostic', 'diagnosticPrecedent', 'diagnosticstatut', 'entrepriseprofil'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir la dernière évolution pour une entreprise
     */
    public static function dernierePourEntreprise($entrepriseId)
    {
        return self::where('entreprise_id', $entrepriseId)
            ->with(['diagnostic', 'diagnosticPrecedent', 'diagnosticstatut', 'entrepriseprofil'])
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Déterminer le diagnosticstatut_id équivalent au profil de l'entreprise
     * en tenant compte de l'évolution du score par rapport au diagnostic précédent
     */
    private static function determinerDiagnosticstatutPourProfil($profilId, $scoreActuel = 0, $scorePrecedent = null)
    {
        // Mapping de base pour chaque profil (Évolution 1)
        $baseMapping = [
            1 => 3, // PÉPITE → "Pépite - Évolution 1 : Premiers pas vers la maturité"
            2 => 6, // ÉMERGENTE → "Émergente - Évolution 1 : Structuration initiale"
            3 => 9, // ÉLITE → "Élite - Évolution 1 : Stabilisation stratégique"
        ];
        
        $statutId = $baseMapping[$profilId] ?? 3;
        
        // Si on a un diagnostic précédent, analyser l'évolution du score
        if ($scorePrecedent !== null && $scorePrecedent > 0) {
            $evolutionScore = $scoreActuel - $scorePrecedent;
            
            // Si le score a évolué, faire évoluer le statut
            if ($evolutionScore > 0) {
                // Déterminer le niveau d'évolution selon le gain de score
                if ($evolutionScore >= 20) {
                    // Grande évolution → Évolution 3
                    $statutId = $baseMapping[$profilId] + 2;
                } elseif ($evolutionScore >= 10) {
                    // Évolution moyenne → Évolution 2
                    $statutId = $baseMapping[$profilId] + 1;
                }
                // Sinon, garder Évolution 1 (petite évolution ou régression)
            }
        }
        
        return $statutId;
    }

    /**
     * Créer une nouvelle évolution
     */
    public static function creerEvolution($entrepriseId, $diagnosticId, $diagnosticPrecedentId = null, $commentaire = null)
    {
        $diagnostic = Diagnostic::find($diagnosticId);
        
        if (!$diagnostic) {
            return null;
        }

        // Récupérer le score du diagnostic précédent si disponible
        $scorePrecedent = null;
        if ($diagnosticPrecedentId) {
            $diagnosticPrecedent = Diagnostic::find($diagnosticPrecedentId);
            if ($diagnosticPrecedent) {
                $scorePrecedent = $diagnosticPrecedent->scoreglobal ?? 0;
            }
        }

        // Déterminer le diagnosticstatut_id équivalent au profil de l'entreprise
        // en tenant compte de l'évolution du score
        $profilId = $diagnostic->entrepriseprofil_id ?? 1;
        $scoreActuel = $diagnostic->scoreglobal ?? 0;
        $diagnosticstatutId = self::determinerDiagnosticstatutPourProfil($profilId, $scoreActuel, $scorePrecedent);

        return self::create([
            'membre_id' => $diagnostic->membre_id,
            'entreprise_id' => $entrepriseId,
            'diagnostic_id' => $diagnosticId,
            'diagnostic_precedent_id' => $diagnosticPrecedentId,
            'score_global' => $diagnostic->scoreglobal ?? 0,
            'diagnosticstatut_id' => $diagnosticstatutId,
            'entrepriseprofil_id' => $diagnostic->entrepriseprofil_id ?? 1,
            'commentaire' => $commentaire,
        ]);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopePeriode($query, $jours)
    {
        return $query->where('created_at', '>=', now()->subDays($jours));
    }

    /**
     * Scope pour les progressions
     */
    public function scopeProgressions($query)
    {
        return $query->whereHas('diagnosticPrecedent', function($q) {
            $q->whereColumn('diagnostics.scoreglobal', '<', 'diagnosticevolutions.score_global');
        });
    }

    /**
     * Scope pour les régressions
     */
    public function scopeRegressions($query)
    {
        return $query->whereHas('diagnosticPrecedent', function($q) {
            $q->whereColumn('diagnostics.scoreglobal', '>', 'diagnosticevolutions.score_global');
        });
    }
}
