<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticreponse extends Model
{
    use HasFactory;

    protected $table = 'diagnosticreponses';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'position',
        'explication',
        'score',
        'langue_id',
        'diagnosticquestion_id',
        'spotlight',
        'etat',
    ];
    

    protected $casts = [
        'position' => 'integer',
        'langue_id' => 'integer',
        'diagnosticquestion_id' => 'integer',
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];
    
    public function diagnosticquestion()
    {
        return $this->belongsTo(Diagnosticquestion::class);
    }

}
