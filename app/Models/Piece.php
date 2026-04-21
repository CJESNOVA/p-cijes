<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{
    use HasFactory;

    protected $table = 'pieces';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'fichier',
        'piecetype_id',
        'datepiece',
        'entreprise_id',
        'spotlight',
        'etat',
    ];

    public function piecetype()
    {
        return $this->belongsTo(Piecetype::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

}
