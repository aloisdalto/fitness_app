<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'age', 'height_cm', 'weight_kg', 'gender'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function routines() {
        return $this->hasMany(Routine::class);
    }
    
    public function workoutSessions() {
        return $this->hasMany(WorkoutSession::class);
    }

    public function sleepLogs() {
        return $this->hasMany(SleepLog::class);
    }

    public function mealLogs() {
        return $this->hasMany(MealLog::class);
    }
}