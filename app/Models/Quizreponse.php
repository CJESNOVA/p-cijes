<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizreponse extends Model
{
    use HasFactory;

    protected $table = 'quizreponses';

    /**
     * @var array
     */
    protected $fillable = [
        'text',
        'correcte',
        'quizquestion_id',
        'spotlight',
        'etat',
    ];

    public function quizquestion()
    {
        return $this->belongsTo(Quizquestion::class);
    }


}
