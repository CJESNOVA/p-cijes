<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bon extends Model
{
    use HasFactory;

    protected $table = 'bons';

    /**
     * @var array
     */
    protected $fillable = [
        'montant',
        'bonstatut_id',
        'bontype_id',
        'datebon',
        'pays_id',
        'entreprise_id',
        'user_id',
        'spotlight',
        'etat',
    ];
    
    public function bonstatut()
    {
        return $this->belongsTo(Bonstatut::class);
    }

    public function bontype()
    {
        return $this->belongsTo(Bontype::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bonutilises()
    {
        return $this->hasMany(Bonutilise::class);
    }

}
