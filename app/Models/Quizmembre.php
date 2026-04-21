<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizmembre extends Model
{
    use HasFactory;

    protected $table = 'quizmembres';

    /**
     * @var array
     */
    protected $fillable = [
        'valeur',
        'membre_id',
        'quizquestion_id',
        'quizreponse_id',
        'spotlight',
        'etat',
    ];

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function quizquestion()
    {
        return $this->belongsTo(Quizquestion::class);
    }

    public function quizreponse()
    {
        return $this->belongsTo(Quizreponse::class);
    }


}
