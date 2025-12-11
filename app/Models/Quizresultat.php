<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizresultat extends Model
{
    use HasFactory;

    protected $table = 'quizresultats';

    /**
     * @var array
     */
    protected $fillable = [
        'score',
        'membre_id',
        'quiz_id',
        'quizresultatstatut_id',
        'spotlight',
        'etat',
    ];

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizresultatstatut()
    {
        return $this->belongsTo(Quizresultatstatut::class);
    }


}
