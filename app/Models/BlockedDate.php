<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedDate extends Model
{
    use HasFactory;

    // Allow these fields to be saved to the database
    protected $fillable = [
        'date',
        'reason',
    ];
}