<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticquestion extends Model
{
    use HasFactory;

    protected $table = 'diagnosticquestions';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'position',
        'diagnosticmodule_id',
        'diagnosticquestiontype_id',
        'diagnosticquestioncategorie_id',
        'langue_id',
        'obligatoire',
        'parent',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'diagnosticmodule_id' => 'integer',
        'diagnosticquestiontype_id' => 'integer',
        'diagnosticquestioncategorie_id' => 'integer',
        'langue_id' => 'integer',
        'obligatoire' => 'boolean',
        'parent' => 'integer',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];
    
    public function diagnosticmodule()
    {
        return $this->belongsTo(Diagnosticmodule::class);
    }

    public function diagnosticquestiontype()
    {
        return $this->belongsTo(Diagnosticquestiontype::class);
    }

    public function diagnosticquestioncategorie()
    {
        return $this->belongsTo(Diagnosticquestioncategorie::class);
    }

    public function langue()
    {
        return $this->belongsTo(Langue::class);
    }

    public function questionparent()
    {
        return $this->belongsTo(Diagnosticquestion::class, 'parent');
    }

    public function diagnosticreponses()
    {
        return $this->hasMany(Diagnosticreponse::class, 'diagnosticquestion_id');
    }

    public function diagnosticresultats()
{
    return $this->hasMany(Diagnosticresultat::class, 'diagnosticquestion_id');
}
}
