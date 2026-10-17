<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'type',
        'school_name',
        'teacher_names',
        'guest_name',
        'parent_1_name',
        'parent_2_name',
        'seat_area',
        'event_date', // <--- Tambahkan ini
        'scan_count',
        'max_scans',
        'status'
    ];

    protected $casts = [
        'teacher_names' => 'array',
        'event_date' => 'date:Y-m-d',
        'scan_count' => 'integer',
        'max_scans' => 'integer',
    ];

    protected $appends = ['status', 'is_full'];

    public function getStatusAttribute(): string
    {
        return $this->scan_count >= $this->max_scans ? 'Kuota penuh' : 'Aktif';
    }

    public function getIsFullAttribute(): bool
    {
        return $this->scan_count >= $this->max_scans;
    }
}