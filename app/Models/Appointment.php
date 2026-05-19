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
        'name',      // <--- Added
        'phone',
        'ips',       // <--- Added
        'purpose',   // <--- Renamed from 'title'
        'location',
        'date',      // <--- Added
        'time',      // <--- Added
        'status',
        'reschedule_reason',
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
