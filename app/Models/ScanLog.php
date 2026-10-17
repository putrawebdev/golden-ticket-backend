<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id', 'scanned_code', 'scanned_by_user_id', 
        'status', 'message', 'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];
}