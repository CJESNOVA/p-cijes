<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnementdocument extends Model
{
    use HasFactory;

    protected $table = 'accompagnementdocuments';

    /**
     * @var array
     */
    protected $fillable = [
        'document_id',
        'accompagnement_id',
        'spotlight',
        'etat',
    ];
    
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function accompagnement()
    {
        return $this->belongsTo(Accompagnement::class);
    }


}
