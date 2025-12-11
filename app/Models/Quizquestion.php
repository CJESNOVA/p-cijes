<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizquestion extends Model
{
    use HasFactory;

    protected $table = 'quizquestions';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'point',
        'quiz_id',
        'quizquestiontype_id',
        'spotlight',
        'etat',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizquestiontype()
    {
        return $this->belongsTo(Quizquestiontype::class);
    }

    public function quizreponses()
    {
        return $this->hasMany(Quizreponse::class);
    }

}
