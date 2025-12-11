<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    /**
     * @var array
     */
    protected $fillable = [
        'membre_id1',
        'membre_id2',
        'spotlight',
        'etat',
    ];
    
    public function membre1()
    {
        return $this->belongsTo(Membre::class, 'membre_id1');
    }

    public function membre2()
    {
        return $this->belongsTo(Membre::class, 'membre_id2');
    }
    
public function messages()
{
    return $this->hasMany(Message::class, 'conversation_id');
}

}
