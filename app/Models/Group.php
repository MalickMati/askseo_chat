<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members');
    }
    protected $fillable = ['name'];
}
