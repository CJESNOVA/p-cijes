<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $table = 'actions';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'code',
        'point',
        'limite',
        'seuil',
        'ressourcetype_id',
        'spotlight',
        'etat',
    ];

    public function ressourcetype()
    {
        return $this->belongsTo(Ressourcetype::class);
    }

}
