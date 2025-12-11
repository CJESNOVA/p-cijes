<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sujet extends Model
{
    use HasFactory;

    protected $table = 'sujets';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'resume',
        'description',
        'vignette',
        'forum_id',
        'membre_id',
        'spotlight',
        'etat',
    ];
    
    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }
    
public function messageforums()
{
    return $this->hasMany(Messageforum::class, 'sujet_id');
}

}
