<?php

namespace App\Http\Controllers;

use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkoutSessionController extends Controller
{
    public function store(Request $request)
    {
        // CORRECCIÓN: Eliminamos 'completed_at' => 'required' porque el usuario no manda la fecha,
        // la generamos nosotros automáticamente en el backend.
        $request->validate([
            'routine_id' => 'required|exists:routines,id',
            'duration_seconds' => 'required|integer',
        ]);

        WorkoutSession::create([
            'user_id' => Auth::id(),
            'routine_id' => $request->routine_id,
            'duration_seconds' => $request->duration_seconds,
            'comments' => $request->comments,
            // Si no viene fecha, usamos AHORA mismo.
            'completed_at' => Carbon::now() 
        ]);

        // Redirigimos al Dashboard para que el usuario vea su estadística actualizada inmediatamente
        return redirect()->route('dashboard')->with('success', '¡Entrenamiento registrado exitosamente!');
    }
}