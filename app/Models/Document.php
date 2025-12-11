<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    /**
     * @var array
     */
    protected $fillable = [
        'titre',
        'fichier',
        'documenttype_id',
        'datedocument',
        'membre_id',
        'spotlight',
        'etat',
    ];

    public function documenttype()
    {
        return $this->belongsTo(Documenttype::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

}
