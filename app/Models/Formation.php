<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $table = 'formations';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'datedebut',
        'datefin',
        'prix',
        'description',
        'expert_id',
        'formationniveau_id',
        'formationtype_id',
        'pays_id',
        'spotlight',
        'etat',
    ];

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function formationniveau()
    {
        return $this->belongsTo(Formationniveau::class);
    }

    public function formationtype()
    {
        return $this->belongsTo(Formationtype::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function quizs()
    {
        return $this->hasMany(Quiz::class);
    }

}
