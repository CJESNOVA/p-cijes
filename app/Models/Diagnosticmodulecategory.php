<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosticmodulecategory extends Model
{
    use HasFactory;

    protected $table = 'diagnosticmodulecategories'; 

    protected $fillable = [
        'titre',
        'etat',
    ];

    /**
     * Relation avec les modules de diagnostic
     */
    public function diagnosticmodules()
    {
        return $this->hasMany(Diagnosticmodule::class, 'diagnosticmodulecategory_id');
    }
}
