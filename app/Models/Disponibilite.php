<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilite extends Model
{
    use HasFactory;

    protected $table = 'disponibilites';

    /**
     * @var array
     */
    protected $fillable = [
        'horairedebut',
        'horairefin',
        'jour_id',
        'expert_id',
        'spotlight',
        'etat',
    ];

    public function jour()
    {
        return $this->belongsTo(Jour::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

}
