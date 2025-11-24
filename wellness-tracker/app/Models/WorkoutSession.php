<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'routine_id', 'duration_seconds', 'comments', 'completed_at'];
    
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function routine() {
        return $this->belongsTo(Routine::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}