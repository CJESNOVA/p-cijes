<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrepriseprofil extends Model
{
    use HasFactory;

    protected $table = 'entrepriseprofils';

    protected $fillable = [
        'titre',
        'etat',
    ];

    public function entreprises()
    {
        return $this->hasMany(Entreprise::class);
    }

    public function cotisationtypes()
    {
        return $this->hasMany(Cotisationtype::class);
    }

    public function reductiontypes()
    {
        return $this->hasMany(Reductiontype::class);
    }
}
