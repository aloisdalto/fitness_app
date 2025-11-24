<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkoutSession;
use App\Models\SleepLog;
use App\Models\MealLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // --- 1. Estadísticas de Ejercicio ---
        $workoutsThisWeek = WorkoutSession::where('user_id', $user->id)
            ->whereBetween('completed_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // CÁLCULO DE RACHA (STREAK)
        // Obtenemos fechas únicas de entrenamiento ordenadas de la más reciente a la más antigua
        $workoutDates = WorkoutSession::where('user_id', $user->id)
            ->where('completed_at', '<=', Carbon::now())
            ->orderBy('completed_at', 'desc')
            ->get()
            ->pluck('completed_at')
            ->map(function ($date) {
                return $date->format('Y-m-d');
            })
            ->unique()
            ->values();

        $streak = 0;
        if ($workoutDates->isNotEmpty()) {
            // Verificar si entrenó hoy o ayer para mantener la racha viva
            $lastWorkout = Carbon::parse($workoutDates[0]);
            if ($lastWorkout->isToday() || $lastWorkout->isYesterday()) {
                $streak = 1;
                $currentCheck = $lastWorkout->copy();
                
                // Iteramos hacia atrás buscando días consecutivos
                for ($i = 1; $i < $workoutDates->count(); $i++) {
                    $prevDate = Carbon::parse($workoutDates[$i]);
                    if ($prevDate->diffInDays($currentCheck) == 1) {
                        $streak++;
                        $currentCheck = $prevDate;
                    } else {
                        break;
                    }
                }
            }
        }

        // --- 2. Estadísticas de Sueño ---
        $avgSleep = SleepLog::where('user_id', $user->id)
            ->orderBy('bed_time', 'desc')
            ->take(7)
            ->get() // Obtenemos la colección para verificar si está vacía luego
            ->avg('duration_hours');

        // --- 3. Estadísticas de Dieta (Calorías) ---
        // Datos para gráfica: Últimos 7 días
        $chartLabels = [];
        $chartDataValues = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $cals = MealLog::where('user_id', $user->id)
                        ->whereDate('eaten_at', $date)
                        ->sum('calories');
            
            $chartLabels[] = $date->format('d/m'); // Ej: 23/11
            $chartDataValues[] = $cals;
        }

        // Verificar si el usuario es nuevo (no tiene datos en absoluto)
        $isNewUser = $workoutDates->isEmpty() && !$avgSleep && array_sum($chartDataValues) == 0;

        return view('dashboard', compact(
            'workoutsThisWeek', 
            'streak', 
            'avgSleep', 
            'chartLabels', 
            'chartDataValues',
            'isNewUser'
        ));
    }
}