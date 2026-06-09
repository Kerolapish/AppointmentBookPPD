<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $casts = [
        'start_time' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'ips',
        'purpose',
        'location',
        'date',
        'time',
        'status',
        'reschedule_reason',
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}