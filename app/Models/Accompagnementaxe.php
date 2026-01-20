<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnementaxe extends Model
{
    use HasFactory;

    protected $table = 'accompagnementaxes';

    protected $fillable = [
        'diagnosticmodule_id',
        'titre',
        'description',
        'spotlight',
        'etat',
    ];

    protected $casts = [
        'spotlight' => 'boolean',
        'etat' => 'boolean',
    ];

    public function diagnosticmodule()
    {
        return $this->belongsTo(Diagnosticmodule::class);
    }

    public function accompagnements()
    {
        return $this->hasMany(Accompagnement::class, 'accompagnementaxe_id');
    }

    public function scopeActif($query)
    {
        return $query->where('etat', true);
    }

    public function scopeSpotlight($query)
    {
        return $query->where('spotlight', true);
    }
}
