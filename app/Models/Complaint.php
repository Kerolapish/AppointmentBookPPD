<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'ips',
        'purpose',       // <--- ADDED THIS so it saves to DB
        'location',
        'incident_date',
        'category',
        'description',
        'attachment',
        'status',        // <--- Good to have if you update status
        'admin_response' // <--- Good to have for admin replies
    ];

    // This is the function that fixes the "RelationNotFound" error
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}