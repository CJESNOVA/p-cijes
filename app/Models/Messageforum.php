<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messageforum extends Model
{
    use HasFactory;

    protected $table = 'messageforums';

    /**
     * @var array
     */
    protected $fillable = [
        'contenu',
        'sujet_id',
        'membre_id',
        'spotlight',
        'etat',
    ];
    
    public function sujet()
    {
        return $this->belongsTo(Sujet::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

}
