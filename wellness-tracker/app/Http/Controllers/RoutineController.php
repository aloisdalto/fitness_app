<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RoutineController extends Controller
{
    // ... (Los métodos index, create, store, show, edit, update, destroy se mantienen IGUALES) ...
    // Solo estoy reemplazando el método de búsqueda al final.

    public function index()
    {
        $routines = Routine::where('user_id', Auth::id())->with('exercises')->get();
        return view('routines.index', compact('routines'));
    }

    public function create()
    {
        return view('routines.create');
    }

    public function store(Request $request)
    {
        $this->validateRoutine($request);

        $routine = Routine::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'days_of_week' => $request->days_of_week
        ]);

        $this->syncExercises($routine, $request->exercises);

        return redirect()->route('routines.index')->with('success', 'Rutina creada exitosamente.');
    }

    public function show(Routine $routine)
    {
        $this->authorizeUser($routine);
        return view('routines.show', compact('routine'));
    }

    public function edit(Routine $routine)
    {
        $this->authorizeUser($routine);
        return view('routines.edit', compact('routine'));
    }

    public function update(Request $request, Routine $routine)
    {
        $this->authorizeUser($routine);
        $this->validateRoutine($request);

        $routine->update([
            'name' => $request->name,
            'description' => $request->description,
            'days_of_week' => $request->days_of_week
        ]);

        $routine->exercises()->detach();
        $this->syncExercises($routine, $request->exercises);

        return redirect()->route('routines.index')->with('success', 'Rutina actualizada.');
    }

    public function destroy(Routine $routine)
    {
        $this->authorizeUser($routine);
        $routine->delete();
        return redirect()->route('routines.index')->with('success', 'Rutina eliminada.');
    }

    // --- Helpers Privados ---
    private function validateRoutine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'days_of_week' => 'required|array',
            'exercises' => 'required|array'
        ]);
    }

    private function authorizeUser(Routine $routine)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
    }

    private function syncExercises(Routine $routine, array $exercisesData)
    {
        foreach ($exercisesData as $exData) {
            $exercise = Exercise::firstOrCreate(
                ['name' => $exData['name']], 
                ['muscle_group' => $exData['muscle_group'] ?? null, 'api_id' => $exData['api_id'] ?? null]
            );

            $routine->exercises()->attach($exercise->id, [
                'suggested_sets' => $exData['sets'] ?? 3,
                'suggested_reps' => $exData['reps'] ?? '10-12'
            ]);
        }
    }

    // --- BÚSQUEDA MOCK (LISTA COMPLETA EN ESPAÑOL) ---
    public function searchExternalExercises(Request $request)
    {
        $query = strtolower($request->get('q'));
        $results = collect();

        // 1. LISTA MAESTRA DE EJERCICIOS (MOCK)
        // Puedes agregar más aquí manualmente si faltan
        $allExercises = collect([
            // PECHO
            ['name' => 'Press Banca Plano (Barra)', 'muscle_group' => 'Pecho', 'id' => 101],
            ['name' => 'Press Banca Inclinado (Mancuernas)', 'muscle_group' => 'Pecho', 'id' => 102],
            ['name' => 'Aperturas con Mancuernas', 'muscle_group' => 'Pecho', 'id' => 103],
            ['name' => 'Cruce de Poleas', 'muscle_group' => 'Pecho', 'id' => 104],
            ['name' => 'Flexiones (Push-ups)', 'muscle_group' => 'Pecho', 'id' => 105],
            ['name' => 'Fondos en Paralelas (Pecho)', 'muscle_group' => 'Pecho', 'id' => 106],
            ['name' => 'Pec Deck (Máquina)', 'muscle_group' => 'Pecho', 'id' => 107],

            // ESPALDA
            ['name' => 'Dominadas', 'muscle_group' => 'Espalda', 'id' => 201],
            ['name' => 'Jalón al Pecho', 'muscle_group' => 'Espalda', 'id' => 202],
            ['name' => 'Remo con Barra', 'muscle_group' => 'Espalda', 'id' => 203],
            ['name' => 'Remo Gironda (Polea Baja)', 'muscle_group' => 'Espalda', 'id' => 204],
            ['name' => 'Remo con Mancuerna (Unilateral)', 'muscle_group' => 'Espalda', 'id' => 205],
            ['name' => 'Peso Muerto Convencional', 'muscle_group' => 'Espalda/Pierna', 'id' => 206],
            ['name' => 'Pull Over (Polea Alta)', 'muscle_group' => 'Espalda', 'id' => 207],
            ['name' => 'Hiperextensiones (Lumbares)', 'muscle_group' => 'Espalda', 'id' => 208],

            // PIERNA
            ['name' => 'Sentadilla Libre (Barra)', 'muscle_group' => 'Piernas', 'id' => 301],
            ['name' => 'Prensa de Piernas', 'muscle_group' => 'Piernas', 'id' => 302],
            ['name' => 'Extensiones de Cuádriceps', 'muscle_group' => 'Piernas', 'id' => 303],
            ['name' => 'Curl Femoral Tumbado', 'muscle_group' => 'Piernas', 'id' => 304],
            ['name' => 'Zancadas (Lunges)', 'muscle_group' => 'Piernas', 'id' => 305],
            ['name' => 'Sentadilla Búlgara', 'muscle_group' => 'Piernas', 'id' => 306],
            ['name' => 'Hip Thrust (Empuje de Cadera)', 'muscle_group' => 'Glúteo', 'id' => 307],
            ['name' => 'Elevación de Talones (Gemelos)', 'muscle_group' => 'Piernas', 'id' => 308],
            ['name' => 'Peso Muerto Rumano', 'muscle_group' => 'Piernas', 'id' => 309],

            // HOMBROS
            ['name' => 'Press Militar (Barra)', 'muscle_group' => 'Hombros', 'id' => 401],
            ['name' => 'Press Militar (Mancuernas)', 'muscle_group' => 'Hombros', 'id' => 402],
            ['name' => 'Elevaciones Laterales', 'muscle_group' => 'Hombros', 'id' => 403],
            ['name' => 'Elevaciones Frontales', 'muscle_group' => 'Hombros', 'id' => 404],
            ['name' => 'Pájaros (Posterior)', 'muscle_group' => 'Hombros', 'id' => 405],
            ['name' => 'Face Pull', 'muscle_group' => 'Hombros', 'id' => 406],
            ['name' => 'Remo al Mentón', 'muscle_group' => 'Hombros', 'id' => 407],

            // BRAZOS (Bíceps/Tríceps)
            ['name' => 'Curl de Bíceps con Barra', 'muscle_group' => 'Brazos', 'id' => 501],
            ['name' => 'Curl Martillo', 'muscle_group' => 'Brazos', 'id' => 502],
            ['name' => 'Curl Predicador (Banco Scott)', 'muscle_group' => 'Brazos', 'id' => 503],
            ['name' => 'Extensiones de Tríceps (Polea)', 'muscle_group' => 'Brazos', 'id' => 504],
            ['name' => 'Press Francés', 'muscle_group' => 'Brazos', 'id' => 505],
            ['name' => 'Fondos entre Bancos', 'muscle_group' => 'Brazos', 'id' => 506],
            ['name' => 'Patada de Tríceps', 'muscle_group' => 'Brazos', 'id' => 507],

            // ABDOMEN
            ['name' => 'Crunch Abdominal', 'muscle_group' => 'Abdomen', 'id' => 601],
            ['name' => 'Plancha Isométrica (Plank)', 'muscle_group' => 'Abdomen', 'id' => 602],
            ['name' => 'Elevación de Piernas', 'muscle_group' => 'Abdomen', 'id' => 603],
            ['name' => 'Rueda Abdominal', 'muscle_group' => 'Abdomen', 'id' => 604],
            ['name' => 'Russian Twists', 'muscle_group' => 'Abdomen', 'id' => 605],

            // CARDIO
            ['name' => 'Cinta de Correr', 'muscle_group' => 'Cardio', 'id' => 701],
            ['name' => 'Bicicleta Estática', 'muscle_group' => 'Cardio', 'id' => 702],
            ['name' => 'Elíptica', 'muscle_group' => 'Cardio', 'id' => 703],
            ['name' => 'Saltar la Cuerda', 'muscle_group' => 'Cardio', 'id' => 704],
            ['name' => 'Remo (Máquina)', 'muscle_group' => 'Cardio', 'id' => 705],
        ]);

        // 2. FILTRAR LISTA (Buscar coincidencia en el nombre)
        // filter() devuelve los items donde la función retorna true
        $filteredResults = $allExercises->filter(function ($value, $key) use ($query) {
            // str_contains busca si el query está dentro del nombre (insensible a mayúsculas por strtolower arriba)
            return str_contains(strtolower($value['name']), $query);
        });

        // 3. AGREGAR OPCIÓN PERSONALIZADA
        // Si no hay resultados exactos, permitimos crear uno nuevo con el nombre que el usuario escribió
        if ($filteredResults->isEmpty() && strlen($query) > 2) {
            $filteredResults->push([
                'name' => ucfirst($request->get('q')), // Usamos el texto original del usuario
                'muscle_group' => 'Personalizado',
                'id' => md5($query),
                'source' => 'new'
            ]);
        }

        // values() reindexa el array para que JSON no se rompa (ej: que no devuelva indices salteados como 0, 5, 12)
        return response()->json(['results' => $filteredResults->values()]);
    }
}