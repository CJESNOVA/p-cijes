<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressourcetypeoffretype extends Model
{
    use HasFactory;

    protected $table = 'ressourcetypeoffretypes';

    /**
     * @var array
     */
    protected $fillable = [
        'ressourcetype_id',
        'offretype_id',
        'table_id',
        'spotlight',
        'etat',
    ];
    
    public function ressourcetype()
    {
        return $this->belongsTo(Ressourcetype::class);
    }

    public function offretype()
    {
        return $this->belongsTo(Offretype::class);
    }

}
