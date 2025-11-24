<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SleepLog extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'bed_time', 'wake_time', 'duration_hours', 'quality_rating'];

    protected $casts = [
        'bed_time' => 'datetime',
        'wake_time' => 'datetime',
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}