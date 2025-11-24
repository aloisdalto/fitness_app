@extends('layouts.app_custom')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    
    <!-- Navegación y Acciones -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <a href="{{ route('routines.index') }}" style="color: var(--text-body); text-decoration: none;">← Volver a Rutinas</a>
        
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('routines.edit', $routine->id) }}" class="btn-primary" style="background: var(--text-head); color: white; padding: 10px 20px; text-decoration: none; border-radius: 50px; font-size: 0.9rem;">
                <i class="bi bi-pencil"></i> Editar
            </a>
            
            <form action="{{ route('routines.destroy', $routine->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta rutina?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #fee2e2; color: #ef4444; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; cursor: pointer;">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    <!-- Header Rutina -->
    <div class="card" style="margin-bottom: 20px; border-left: 5px solid var(--primary);">
        <h2 style="margin-bottom: 10px;">{{ $routine->name }}</h2>
        <p style="color: var(--text-body); margin-bottom: 15px;">{{ $routine->description }}</p>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @foreach($routine->days_of_week as $day)
                <span style="background: var(--accent-soft); color: var(--text-head); padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 600;">
                    {{ $day }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Lista de Ejercicios -->
    <h3 style="margin-bottom: 15px;">Plan de Ejercicios</h3>
    <div class="card">
        @foreach($routine->exercises as $exercise)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #eee;">
                <div>
                    <strong style="font-size: 1.1rem;">{{ $exercise->name }}</strong>
                    <div style="color: #888; font-size: 0.9rem;">{{ $exercise->muscle_group }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; color: var(--primary); font-size: 1.2rem;">
                        {{ $exercise->pivot->suggested_sets }} x {{ $exercise->pivot->suggested_reps }}
                    </div>
                    <small style="color: #aaa;">Series x Reps</small>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Botón Flotante para Iniciar -->
    <div style="margin-top: 30px; text-align: center;">
        <button onclick="window.location.href='{{ route('routines.index') }}'" class="btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">
            Ir al listado para Iniciar Rutina
        </button>
    </div>

</div>
@endsection