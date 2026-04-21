<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    /**
     * @var array
     */
    protected $fillable = [
        'note',
        'commentaire',
        'membre_id',
        'expert_id',
        'spotlight',
        'etat',
    ];
    
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

}
