<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    /**
     * @var array
     */
    protected $fillable = [
        'contenu',
        'conversation_id',
        'membre_id',
        'lu',
        'etat',
    ];
    
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

}
