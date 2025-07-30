<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessageRead extends Model
{
    protected $fillable = ['message_id', 'user_id', 'read_at'];
    
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
