<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $table = 'forums';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'resume',
        'description',
        'langue_id',
        'vignette',
        'forumtype_id',
        'secteur_id',
        'dateforum',
        'pays_id',
        'spotlight',
        'etat',
    ];
    
    public function langue()
    {
        return $this->belongsTo(Langue::class);
    }

    public function forumtype()
    {
        return $this->belongsTo(Forumtype::class);
    }

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }
    
public function sujets()
{
    return $this->hasMany(Sujet::class);
}

}
