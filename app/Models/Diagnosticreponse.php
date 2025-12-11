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
        'score',
        'langue_id',
        'diagnosticquestion_id',
        'spotlight',
        'etat',
    ];
    

    public function diagnosticquestion()
    {
        return $this->belongsTo(Diagnosticquestion::class);
    }

}
