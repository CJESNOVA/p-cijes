<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticblocstatut extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'titre',
        'description',
    ];

    /**
     * Relation avec les scores de modules
     */
    public function diagnosticmodulescores()
    {
        return $this->hasMany(Diagnosticmodulescore::class, 'diagnosticblocstatut_id');
    }

    /**
     * Obtenir un bloc par son code
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }

    /**
     * Scope pour rechercher par code ou titre
     */
    public function scopeRechercher($query, $terme)
    {
        return $query->where(function($q) use ($terme) {
            $q->where('code', 'like', "%{$terme}%")
              ->orWhere('titre', 'like', "%{$terme}%");
        });
    }

    /**
     * Obtenir la liste des blocs pour un select
     */
    public static function getListePourSelect()
    {
        return self::orderBy('code')->pluck('titre', 'id');
    }

    /**
     * Obtenir les blocs principaux prédéfinis
     */
    public static function getBlocsPrincipaux()
    {
        return [
            'critique' => 'Bloc critique',
            'fragile' => 'Bloc fragile',
            'intermediaire' => 'Bloc intermédiaire',
            'conforme' => 'Bloc conforme',
            'reference' => 'Bloc de référence CJES',
        ];
    }

    /**
     * Créer les blocs principaux s'ils n'existent pas
     */
    public static function creerBlocsPrincipaux()
    {
        $blocs = self::getBlocsPrincipaux();
        
        foreach ($blocs as $code => $titre) {
            self::firstOrCreate([
                'code' => $code
            ], [
                'titre' => $titre,
                'description' => "Bloc {$titre} pour l'évaluation diagnostique"
            ]);
        }
    }

    /**
     * Obtenir le niveau de performance selon le code
     */
    public function getNiveauPerformance()
    {
        $niveaux = [
            'critique' => 0,
            'fragile' => 1,
            'intermediaire' => 2,
            'conforme' => 3,
            'reference' => 4,
        ];

        return $niveaux[$this->code] ?? 0;
    }

    /**
     * Obtenir la couleur associée au niveau
     */
    public function getCouleur()
    {
        $couleurs = [
            'critique' => '#dc2626',     // rouge
            'fragile' => '#f97316',      // orange
            'intermediaire' => '#eab308', // jaune
            'conforme' => '#22c55e',     // vert
            'reference' => '#3b82f6',    // bleu
        ];

        return $couleurs[$this->code] ?? '#6b7280';
    }

    /**
     * Vérifier si le bloc est considéré comme bloquant
     */
    public function estBloquant()
    {
        return in_array($this->code, ['critique', 'fragile']);
    }

    /**
     * Obtenir les blocs par niveau de performance
     */
    public static function getByNiveau($niveau)
    {
        $codesParNiveau = [
            0 => ['critique'],
            1 => ['fragile'],
            2 => ['intermediaire'],
            3 => ['conforme'],
            4 => ['reference'],
        ];

        $codes = $codesParNiveau[$niveau] ?? [];
        
        return self::whereIn('code', $codes)->get();
    }
}
