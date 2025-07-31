<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'date',
        'status',
        'check_in',
        'check_out',
        'hours_worked',
        'notes',
        'checkout_method',
    ];

    // Casts for proper data handling
    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status);
    }
}
