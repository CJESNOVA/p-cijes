<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membre extends Model
{
    use HasFactory;

    protected $table = 'membres';

    /**
     * @var array
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'membrestatut_id',
        'vignette',
        'membretype_id',
        'user_id',
        'pays_id',
        'telephone',
        'etat',
    ];
    
    public function membrestatut()
    {
        return $this->belongsTo(Membrestatut::class);
    }

    public function membretype()
    {
        return $this->belongsTo(Membretype::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function entreprisemembres()
{
    return $this->hasMany(Entreprisemembre::class, 'membre_id');
}

public function entreprises()
{
    return $this->belongsToMany(Entreprise::class, 'entreprisemembres', 'membre_id', 'entreprise_id');
}

}
