<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprisemembre extends Model
{
    use HasFactory;

    protected $table = 'entreprisemembres';

    /**
     * @var array
     */
    protected $fillable = [
        'fonction',
        'bio',
        'membre_id',
        'entreprise_id',
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

}
