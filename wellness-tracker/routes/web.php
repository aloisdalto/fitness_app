<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\WorkoutSessionController;
use App\Http\Controllers\SleepLogController;
use App\Http\Controllers\MealLogController;
use Illuminate\Support\Facades\Auth; 

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(); 

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutinas y Ejercicios
    Route::resource('routines', RoutineController::class);
    Route::get('/api/exercises/search', [RoutineController::class, 'searchExternalExercises'])->name('exercises.search');
    
    // Sesiones de Entrenamiento
    Route::post('/workout/store', [WorkoutSessionController::class, 'store'])->name('workout.store');

    // Sueño
    Route::resource('sleep', SleepLogController::class)->only(['index', 'store', 'destroy']);

    // Dieta (ACTUALIZADO: Agregamos destroy)
    Route::resource('diet', MealLogController::class)->only(['index', 'store', 'destroy']);
    Route::post('/diet/analyze-image', [MealLogController::class, 'analyzeImage'])->name('diet.analyze');
});