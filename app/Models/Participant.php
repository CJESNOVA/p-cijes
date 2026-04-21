<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    /**
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'formation_id',
        'dateparticipant',
        'participantstatut_id',
        'spotlight',
        'etat',
    ];
    
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function participantstatut()
    {
        return $this->belongsTo(Participantstatut::class);
    }

}
