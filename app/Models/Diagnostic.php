<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    use HasFactory;

    protected $table = 'diagnostics';

    /**
     * @var array
     */
    protected $fillable = [
        'scoreglobal',
        'commentaire',
        'accompagnement_id',
        'diagnostictype_id',
        'diagnosticstatut_id',
        'membre_id',
        'entreprise_id',
        'entrepriseprofil_id',
        'spotlight',
        'etat',
    ];
    
    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
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

}
