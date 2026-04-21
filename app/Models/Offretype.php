<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offretype extends Model
{
    use HasFactory;

    protected $table = 'offretypes'; 

    protected $fillable = [
        'titre',
        'etat',
    ];

    public function reductiontypes()
    {
        return $this->hasMany(Reductiontype::class);
    }
}
