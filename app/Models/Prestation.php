<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestation extends Model
{
    use HasFactory;

    protected $table = 'prestations';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'prix',
        'duree',
        'description',
        'entreprise_id',
        'prestationtype_id',
        'pays_id',
        'spotlight',
        'etat',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function prestationtype()
    {
        return $this->belongsTo(Prestationtype::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

}
