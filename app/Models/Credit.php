<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $table = 'credits';

    /**
     * @var array
     */
    protected $fillable = [
        'montanttotal',
        'montantutilise',
        'creditstatut_id',
        'credittype_id',
        'datecredit',
        'entreprise_id',
        'pays_id',
        'partenaire_id',
        'user_id',
        'spotlight',
        'etat',
    ];
    
    public function creditstatut()
    {
        return $this->belongsTo(Creditstatut::class);
    }

    public function credittype()
    {
        return $this->belongsTo(Credittype::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function partenaire()
    {
        return $this->belongsTo(Partenaire::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

}
