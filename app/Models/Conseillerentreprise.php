<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conseillerentreprise extends Model
{
    use HasFactory;

    protected $table = 'conseillerentreprises';

    /**
     * @var array
     */
    protected $fillable = [
        'conseiller_id',
        'entreprise_id',
        'spotlight',
        'etat',
    ];
    
    public function conseiller()
    {
        return $this->belongsTo(Conseiller::class, 'conseiller_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

}
