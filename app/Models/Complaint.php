<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    // This tells Laravel which columns are safe to insert data into
    protected $fillable = [
        'name',
        'email',
        'ips',
        'purpose',
        'location',
    ];
}