@extends('layouts.app_custom')

@section('content')

<!-- Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2>Mis Rutinas</h2>
        <p style="color: var(--text-body);">Selecciona una rutina para comenzar a entrenar</p>
    </div>
    <a href="{{ route('routines.create') }}" class="btn-primary" style="text-decoration: none; padding: 12px 25px; border-radius: 50px; background: var(--primary); color: var(--text-head); font-weight: 600;">
        + Nueva Rutina
    </a>
</div>

<!-- Grid de Rutinas -->
<div class="grid-3">
    @forelse($routines as $routine)
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative;">
            
            <!-- BARRA DE ACCIONES (NUEVO) -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;">
                <a href="{{ route('routines.show', $routine->id) }}" title="Ver Detalles" style="color: var(--text-body); font-size: 1.1rem; text-decoration: none;">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('routines.edit', $routine->id) }}" title="Editar Rutina" style="color: var(--primary); font-size: 1.1rem; text-decoration: none;">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <!-- Formulario para eliminar -->
                <form action="{{ route('routines.destroy', $routine->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta rutina?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 1.1rem; cursor: pointer;" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; align-items: center;">
                    <h4 style="margin: 0;">{{ $routine->name }}</h4>
                    <span style="font-size: 0.75rem; background: #e0dbf3; padding: 2px 8px; border-radius: 10px; color: #555;">
                        {{ is_array($routine->days_of_week) ? count($routine->days_of_week) : 0 }} días/sem
                    </span>
                </div>
                
                <p style="font-size: 0.9rem; color: #888; margin-bottom: 15px; min-height: 40px;">
                    {{ Str::limit($routine->description ?? 'Sin descripción', 60) }}
                </p>

                <!-- Pequeña lista de ejercicios (Preview) -->
                <div style="margin-bottom: 20px;">
                    <small style="font-weight: 600; color: var(--text-head);">Ejercicios:</small>
                    <ul style="padding-left: 20px; margin-top: 5px; font-size: 0.85rem; color: var(--text-body);">
                        @foreach($routine->exercises->take(3) as $ex)
                            <li>{{ $ex->name }}</li>
                        @endforeach
                        @if($routine->exercises->count() > 3)
                            <li style="list-style: none; color: #aaa;">+ {{ $routine->exercises->count() - 3 }} más...</li>
                        @endif
                    </ul>
                </div>
            </div>

            <button onclick="openWorkoutModal({{ $routine->id }}, '{{ $routine->name }}')" 
                    style="width: 100%; padding: 10px; background: var(--text-head); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="bi bi-play-circle-fill"></i> Iniciar Rutina
            </button>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
            <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #ddd;"></i>
            <p>No tienes rutinas creadas aún.</p>
        </div>
    @endforelse
</div>

<!-- MODAL DE ENTRENAMIENTO -->
<div id="workoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px; text-align: center; position: relative;">
        
        <h3 id="modalRoutineName">Nombre Rutina</h3>
        <p style="color: #888;">Entrenamiento en curso...</p>

        <div style="font-size: 4rem; font-weight: 700; color: var(--text-head); font-variant-numeric: tabular-nums; margin: 20px 0;">
            <span id="timerDisplay">00:00:00</span>
        </div>

        <div style="margin-bottom: 30px; display: flex; gap: 10px; justify-content: center;">
            <button id="btnPause" onclick="pauseTimer()" style="padding: 8px 15px; border-radius: 8px; border: 1px solid #ccc; background: white; cursor: pointer;">Pausar</button>
            <button id="btnResume" onclick="resumeTimer()" style="padding: 8px 15px; border-radius: 8px; background: var(--text-head); color: white; border: none; cursor: pointer; display: none;">Reanudar</button>
        </div>

        <form action="{{ route('workout.store') }}" method="POST">
            @csrf
            <input type="hidden" name="routine_id" id="modalRoutineId">
            <!-- Corregido: Nos aseguramos que este input se actualice -->
            <input type="hidden" name="duration_seconds" id="modalDurationInput" value="0">
            
            <div style="text-align: left; margin-bottom: 20px;">
                <label style="font-size: 0.9rem; font-weight: 600;">Notas de la sesión:</label>
                <textarea name="comments" rows="3" class="form-control" placeholder="¿Qué tal te sentiste? ¿Subiste pesos?" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #ddd; margin-top: 5px;"></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer;">
                Terminar y Guardar
            </button>
        </form>

        <button onclick="closeWorkoutModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>
</div>

<script>
    let timerInterval;
    let seconds = 0;
    let isPaused = false;
    const modal = document.getElementById('workoutModal');
    const timerDisplay = document.getElementById('timerDisplay');
    const durationInput = document.getElementById('modalDurationInput');

    function openWorkoutModal(routineId, routineName) {
        seconds = 0;
        isPaused = false;
        timerDisplay.textContent = "00:00:00";
        durationInput.value = 0; // Reiniciar input
        document.getElementById('modalRoutineId').value = routineId;
        document.getElementById('modalRoutineName').textContent = routineName;
        
        modal.style.display = 'flex';
        startTimer();
    }

    function closeWorkoutModal() {
        if(confirm("¿Cancelar entrenamiento? No se guardará el progreso.")) {
            clearInterval(timerInterval);
            modal.style.display = 'none';
        }
    }

    function startTimer() {
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            if(!isPaused) {
                seconds++;
                updateDisplay();
                // IMPORTANTE: Actualizar el input oculto cada segundo
                durationInput.value = seconds;
            }
        }, 1000);
    }

    function pauseTimer() {
        isPaused = true;
        document.getElementById('btnPause').style.display = 'none';
        document.getElementById('btnResume').style.display = 'inline-block';
    }

    function resumeTimer() {
        isPaused = false;
        document.getElementById('btnPause').style.display = 'inline-block';
        document.getElementById('btnResume').style.display = 'none';
    }

    function updateDisplay() {
        const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        timerDisplay.textContent = `${h}:${m}:${s}`;
    }
</script>
@endsection