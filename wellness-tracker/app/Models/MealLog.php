<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'name', 'image_path', 
        'calories', 'protein_g', 'carbs_g', 'fats_g', 'eaten_at'
    ];

    protected $casts = [
        'eaten_at' => 'datetime',
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}