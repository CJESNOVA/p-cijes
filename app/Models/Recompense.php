<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recompense extends Model
{
    use HasFactory;

    protected $table = 'recompenses';


    /**
     * @var array
     */
    protected $fillable = [
        'valeur',
        'commentaire',
        'action_id',
        'ressourcetype_id',
        'dateattribution',
        'membre_id',
        'entreprise_id',
        'source_id',
        'spotlight',
        'etat',
    ];
    
    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function ressourcetype()
    {
        return $this->belongsTo(Ressourcetype::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }


}
