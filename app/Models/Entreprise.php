<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    protected $table = 'entreprises';

    /**
     * @var array
     */
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'description',
        'secteur_id',
        'vignette',
        'entreprisetype_id',
        'entrepriseprofil_id',
        'est_membre_cijes',
        'annee_creation',
        'pays_id',
        'supabase_startup_id',
        'spotlight',
        'etat',
    ];
    
    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function entreprisetype()
    {
        return $this->belongsTo(Entreprisetype::class);
    }

    public function entrepriseprofil()
    {
        return $this->belongsTo(Entrepriseprofil::class);
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function membres()
    {
        return $this->hasMany(Membre::class);
    }

    public function entreprisesmembres()
    {
        return $this->hasMany(Entreprisemembre::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function diagnostics()
    {
        return $this->hasMany(Diagnostic::class);
    }

    /**
     * Génère un numéro d'identifiant pour l'affichage uniquement
     */
    public function getNumeroIdentifiantAttribute()
    {
        $prefixe = 'CJESTG00';
        $annee = date('y', strtotime($this->created_at)); // Deux derniers chiffres de l'année de création
        $mois = date('m', strtotime($this->created_at)); // Deux derniers chiffres du mois de création
        
        // Utiliser l'ID de l'entreprise comme numéro d'ordre pour l'affichage
        $ordre = str_pad($this->id, 5, '0', STR_PAD_LEFT);
        
        return $prefixe . $annee . $mois . $ordre;
    }

}
