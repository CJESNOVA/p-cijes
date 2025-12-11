<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnement extends Model
{
    use HasFactory;

    protected $table = 'accompagnements';

    /**
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'entreprise_id',
        'accompagnementniveau_id',
        'dateaccompagnement',
        'accompagnementstatut_id',
        'spotlight',
        'etat',
    ];
    
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }
    
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function accompagnementniveau()
    {
        return $this->belongsTo(Accompagnementniveau::class);
    }

    public function accompagnementstatut()
    {
        return $this->belongsTo(Accompagnementstatut::class);
    }

}
