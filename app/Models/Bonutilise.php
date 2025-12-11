<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonutilise extends Model
{
    use HasFactory;

    protected $table = 'bonutilises';

    /**
     * @var array
     */
    protected $fillable = [
        'montant',
        'noteservice',
        'bon_id',
        'prestationrealisee_id',
        'spotlight',
        'etat',
    ];

    public function bon()
    {
        return $this->belongsTo(Bon::class);
    }

    public function prestationrealisee()
    {
        return $this->belongsTo(Prestationrealisee::class);
    }

}
