<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    use HasFactory;

    protected $table = 'diagnostics';

    protected $appends = ['nom_complet'];

    /**
     * @var array
     */
    protected $fillable = [
        'scoreglobal',
        'commentaire',
        'diagnostictype_id',
        'diagnosticstatut_id',
        'membre_id',
        'entreprise_id',
        'entrepriseprofil_id',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'diagnostictype_id' => 'integer',
        'diagnosticstatut_id' => 'integer',
        'membre_id' => 'integer',
        'entreprise_id' => 'integer',
        'entrepriseprofil_id' => 'integer',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];
    
    public function accompagnements()
    {
        return $this->hasMany(Accompagnement::class);
    }

    public function accompagnement()
    {
        return $this->hasOne(Accompagnement::class);
    }

    public function diagnostictype()
    {
        return $this->belongsTo(Diagnostictype::class);
    }

    public function diagnosticstatut()
    {
        return $this->belongsTo(Diagnosticstatut::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function entrepriseprofil()
    {
        return $this->belongsTo(Entrepriseprofil::class);
    }

    public function diagnosticmodulescores()
    {
        return $this->hasMany(Diagnosticmodulescore::class);
    }

    public function diagnosticresultats()
    {
        return $this->hasMany(Diagnosticresultat::class);
    }

    public function getNomCompletAttribute(): string
    {
        $membre = $this->membre ? "{$this->membre->prenom} {$this->membre->nom}" : '';
        $entreprise = $this->entreprise ? $this->entreprise->nom : '';
        return trim("Diagnostic #{$this->id} - $membre - $entreprise (Score: {$this->scoreglobal})");
    }
}
