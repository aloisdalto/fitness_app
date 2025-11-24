<?php

namespace App\Http\Controllers;

use App\Models\SleepLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SleepLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtenemos todos los logs ordenados para el historial
        $logs = SleepLog::where('user_id', $user->id)
            ->orderBy('bed_time', 'desc')
            ->get();

        // --- CÁLCULO DE ESTADÍSTICAS ---
        
        // 1. Promedio histórico
        $avgDuration = $logs->avg('duration_hours');
        
        // 2. Calidad promedio
        $avgQuality = $logs->avg('quality_rating');

        // 3. Mejor racha (noche con más sueño)
        $bestNight = $logs->max('duration_hours');

        // --- PREPARAR DATOS PARA LA GRÁFICA (Últimos 7 registros invertidos para cronología) ---
        $chartData = $logs->take(7)->reverse();
        
        $chartLabels = $chartData->map(function ($log) {
            return Carbon::parse($log->bed_time)->format('d/m');
        })->values();

        $chartValues = $chartData->pluck('duration_hours')->values();

        return view('sleep.index', compact('logs', 'avgDuration', 'avgQuality', 'bestNight', 'chartLabels', 'chartValues'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bed_time' => 'required|date',
            'wake_time' => 'required|date|after:bed_time',
            'quality_rating' => 'nullable|integer|min:1|max:5'
        ]);

        $start = Carbon::parse($request->bed_time);
        $end = Carbon::parse($request->wake_time);
        
        // Calculamos duración con decimales (Ej: 7.5 horas)
        $duration = $end->diffInHours($start) + ($end->diffInMinutes($start) % 60) / 60;

        SleepLog::create([
            'user_id' => Auth::id(),
            'bed_time' => $start,
            'wake_time' => $end,
            'duration_hours' => $duration,
            'quality_rating' => $request->quality_rating
        ]);

        return redirect()->route('sleep.index')->with('success', 'Descanso registrado correctamente.');
    }

    public function destroy(SleepLog $sleep)
    {
        if ($sleep->user_id !== Auth::id()) {
            abort(403);
        }
        $sleep->delete();
        return redirect()->route('sleep.index')->with('success', 'Registro eliminado.');
    }
}