@extends('layouts.app_custom')

@section('content')
<style>
    /* Estilos específicos para el constructor de rutinas */
    .search-box {
        position: relative;
        margin-bottom: 20px;
    }
    
    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--accent-soft);
        border-radius: 0 0 10px 10px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: none; /* Oculto por defecto */
    }

    .result-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .result-item:hover {
        background-color: #f9f9f9;
    }

    .added-exercise-card {
        background: white;
        border: 1px solid var(--accent-soft);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        transition: 0.3s;
    }

    .added-exercise-card:hover {
        border-color: var(--primary);
    }

    .days-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }

    .day-check input[type="checkbox"] {
        display: none;
    }

    .day-check label {
        display: block;
        padding: 10px;
        text-align: center;
        background: white;
        border: 1px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
        font-weight: 500;
    }

    .day-check input:checked + label {
        background: var(--primary);
        color: var(--text-head);
        border-color: var(--primary);
    }
</style>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('routines.index') }}" style="color: var(--text-body); text-decoration: none;">← Volver a Rutinas</a>
        <h2 style="margin-top: 10px;">Diseñar Nueva Rutina</h2>
    </div>

    <form action="{{ route('routines.store') }}" method="POST" id="routineForm">
        @csrf

        <!-- Info Básica -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600;">Nombre de la Rutina</label>
                <input type="text" name="name" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;" placeholder="Ej: Empuje (Pecho/Tríceps)" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600;">Descripción (Opcional)</label>
                <textarea name="description" rows="2" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;"></textarea>
            </div>

            <label style="font-weight: 600; margin-bottom: 10px; display: block;">Días Sugeridos</label>
            <div class="days-grid">
                @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $day)
                <div class="day-check">
                    <input type="checkbox" name="days_of_week[]" value="{{ $day }}" id="day_{{ $loop->index }}">
                    <label for="day_{{ $loop->index }}">{{ $day }}</label>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Buscador de Ejercicios -->
        <div class="card">
            <h4 style="margin-bottom: 15px;">Agregar Ejercicios</h4>
            
            <div class="search-box">
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="exerciseSearch" placeholder="Buscar ejercicio (ej: Press Banca)..." style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
                    <button type="button" onclick="searchExercises()" class="btn-primary" style="border:none; padding: 0 20px; border-radius: 8px; cursor: pointer;">Buscar</button>
                </div>
                <!-- Lista Desplegable de Resultados -->
                <div id="searchResults" class="search-results"></div>
            </div>

            <!-- Lista de ejercicios seleccionados -->
            <div id="selectedExercisesList">
                <p style="text-align: center; color: #aaa; margin: 20px 0;" id="emptyMsg">
                    No has agregado ejercicios aún.
                </p>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: right;">
            <button type="submit" class="btn-primary" style="padding: 12px 30px; border: none; border-radius: 50px; font-weight: 600; cursor: pointer;">Guardar Rutina Completa</button>
        </div>
    </form>
</div>

<!-- Scripts JS para la interactividad -->
<script>
    let exerciseIndex = 0;

    // 1. Función para buscar ejercicios (Llama a tu API Backend)
    async function searchExercises() {
        const query = document.getElementById('exerciseSearch').value;
        if(query.length < 2) return;

        const resultsDiv = document.getElementById('searchResults');
        resultsDiv.innerHTML = '<div style="padding:10px;">Buscando...</div>';
        resultsDiv.style.display = 'block';

        try {
            // Llamada a la ruta que definimos en Laravel
            const response = await fetch(`{{ route('exercises.search') }}?q=${query}`);
            const data = await response.json();

            resultsDiv.innerHTML = '';
            
            if(data.results && data.results.length > 0) {
                data.results.forEach(ex => {
                    const div = document.createElement('div');
                    div.className = 'result-item';
                    div.innerHTML = `
                        <div>
                            <strong>${ex.name}</strong>
                            <small style="color: #888; margin-left: 5px;">${ex.muscle_group || 'General'}</small>
                        </div>
                        <button type="button" style="background: var(--primary); border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">+</button>
                    `;
                    // Al hacer click, agregamos el ejercicio
                    div.onclick = () => addExerciseToRoutine(ex);
                    resultsDiv.appendChild(div);
                });
            } else {
                resultsDiv.innerHTML = '<div style="padding:10px;">No se encontraron resultados.</div>';
            }

        } catch (error) {
            console.error(error);
            resultsDiv.innerHTML = '<div style="padding:10px; color: red;">Error al buscar.</div>';
        }
    }

    // 2. Función para agregar el ejercicio visualmente al formulario
    function addExerciseToRoutine(exercise) {
        document.getElementById('emptyMsg').style.display = 'none';
        document.getElementById('searchResults').style.display = 'none';
        document.getElementById('exerciseSearch').value = ''; // Limpiar buscador

        const container = document.getElementById('selectedExercisesList');
        const card = document.createElement('div');
        card.className = 'added-exercise-card';
        card.innerHTML = `
            <div style="flex: 2;">
                <strong>${exercise.name}</strong><br>
                <small>${exercise.muscle_group || 'General'}</small>
                
                <!-- Inputs Ocultos para enviar al backend -->
                <input type="hidden" name="exercises[${exerciseIndex}][name]" value="${exercise.name}">
                <input type="hidden" name="exercises[${exerciseIndex}][muscle_group]" value="${exercise.muscle_group || ''}">
                <input type="hidden" name="exercises[${exerciseIndex}][api_id]" value="${exercise.id || ''}">
            </div>
            
            <div style="flex: 1;">
                <label style="font-size: 0.8rem;">Series</label>
                <input type="number" name="exercises[${exerciseIndex}][sets]" value="3" style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="flex: 1;">
                <label style="font-size: 0.8rem;">Reps</label>
                <input type="text" name="exercises[${exerciseIndex}][reps]" value="10-12" style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <button type="button" onclick="this.parentElement.remove()" style="background: #ffaaaa; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; color: white;">X</button>
        `;

        container.appendChild(card);
        exerciseIndex++;
    }

    // Cerrar buscador si hago click fuera
    document.addEventListener('click', function(e) {
        if (!document.querySelector('.search-box').contains(e.target)) {
            document.getElementById('searchResults').style.display = 'none';
        }
    });
</script>
@endsection