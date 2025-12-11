<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    use HasFactory;

    protected $table = 'partenaires';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'contact',
        'description',
        'langue_id',
        'vignette',
        'partenairetype_id',
        'pays_id',
        'spotlight',
        'etat',
    ];
    
    public function langue()
    {
        return $this->belongsTo(Langue::class);
    }

    public function partenairetype()
    {
        return $this->belongsTo(Partenairetype::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

}
