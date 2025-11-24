<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'api_id', 'muscle_group'];
    
    public function routines() {
        return $this->belongsToMany(Routine::class, 'exercise_routine');
    }
}