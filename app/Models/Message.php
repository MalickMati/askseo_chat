<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'group_id',
        'file_path',
        'sent_at',
        'read_at',
        'file_extension',
    ];

    protected $dates = ['sent_at', 'read_at'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function reads()
    {
        return $this->hasMany(GroupMessageRead::class, 'message_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Karachi');
    }

    public function getSentAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Karachi');
    }
}
