<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnement extends Model
{
    use HasFactory;

    protected $table = 'accompagnements';

    protected $appends = ['nom_complet'];

    /**
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'entreprise_id',
        'diagnostic_id',
        'accompagnementniveau_id',
        'dateaccompagnement',
        'accompagnementstatut_id',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'membre_id' => 'integer',
        'entreprise_id' => 'integer',
        'diagnostic_id' => 'integer',
        'accompagnementniveau_id' => 'integer',
        'dateaccompagnement' => 'date',
        'accompagnementstatut_id' => 'integer',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
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

    public function accompagnementniveau()
    {
        return $this->belongsTo(Accompagnementniveau::class);
    }

    public function accompagnementstatut()
    {
        return $this->belongsTo(Accompagnementstatut::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
    
    public function diagnostics()
    {
        return $this->hasMany(Diagnostic::class);
    }

    public function propositions()
    {
        return $this->hasMany(Proposition::class);
    }

    public function getNomCompletAttribute(): string
    {
        $membre = $this->membre ? "{$this->membre->prenom} {$this->membre->nom}" : '';
        $entreprise = $this->entreprise ? $this->entreprise->nom : '';
        return trim("$membre - $entreprise");
    }
}
