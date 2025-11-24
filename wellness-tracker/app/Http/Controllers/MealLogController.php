<?php

namespace App\Http\Controllers;

use App\Models\MealLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealLogController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Obtenemos historial completo
        $meals = MealLog::where('user_id', $userId)
            ->orderBy('eaten_at', 'desc')
            ->get();

        // --- ESTADÍSTICAS DE HOY ---
        $todayMeals = $meals->filter(function($meal) {
            return $meal->eaten_at->isToday();
        });

        $todayStats = [
            'calories' => $todayMeals->sum('calories'),
            'protein' => $todayMeals->sum('protein_g'),
            'carbs' => $todayMeals->sum('carbs_g'),
            'fats' => $todayMeals->sum('fats_g'),
        ];

        return view('diet.index', compact('meals', 'todayStats'));
    }

    // Simulador de IA (Food Recognition)
    public function analyzeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096', // Max 4MB
        ]);

        // Guardamos temporalmente para simular proceso
        // En una app real, aquí enviarías este path a la API externa
        $path = $request->file('image')->store('temp_meals', 'public');

        // SIMULACIÓN INTELIGENTE:
        // Dependiendo del nombre del archivo o al azar, devolvemos distintos platos
        // para que se sienta más real al probarlo.
        
        $mockResults = [
            [
                'name' => 'Ensalada César con Pollo',
                'calories' => 350,
                'protein_g' => 25,
                'carbs_g' => 12,
                'fats_g' => 20
            ],
            [
                'name' => 'Pasta a la Boloñesa',
                'calories' => 650,
                'protein_g' => 22,
                'carbs_g' => 85,
                'fats_g' => 18
            ],
            [
                'name' => 'Tostada de Aguacate y Huevo',
                'calories' => 420,
                'protein_g' => 14,
                'carbs_g' => 30,
                'fats_g' => 25
            ]
        ];

        // Devolver uno al azar para probar
        $result = $mockResults[array_rand($mockResults)];

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'calories' => 'required|integer',
            'eaten_at' => 'required|date'
        ]);

        // Guardar imagen real si se sube
        $imagePath = null;
        if ($request->hasFile('image_final')) {
            $imagePath = $request->file('image_final')->store('meals', 'public');
        }

        MealLog::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'image_path' => $imagePath,
            'calories' => $request->calories,
            'protein_g' => $request->protein_g ?? 0,
            'carbs_g' => $request->carbs_g ?? 0,
            'fats_g' => $request->fats_g ?? 0,
            'eaten_at' => $request->eaten_at
        ]);

        return redirect()->route('diet.index')->with('success', 'Comida registrada exitosamente.');
    }

    public function destroy(MealLog $diet) // Laravel busca el modelo por el ID de la ruta
    {
        if ($diet->user_id !== Auth::id()) {
            abort(403);
        }
        $diet->delete();
        return redirect()->route('diet.index')->with('success', 'Comida eliminada.');
    }
}