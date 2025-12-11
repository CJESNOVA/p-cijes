<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizs';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'seuil_reussite',
        'formation_id',
        'spotlight',
        'etat',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function quizquestions()
    {
        return $this->hasMany(Quizquestion::class);
    }

}
