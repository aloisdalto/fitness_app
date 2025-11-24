<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'days_of_week'];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function exercises() {
        return $this->belongsToMany(Exercise::class, 'exercise_routine')
                    ->withPivot('suggested_sets', 'suggested_reps');
    }
    
    public function sessions() {
        return $this->hasMany(WorkoutSession::class);
    }
}