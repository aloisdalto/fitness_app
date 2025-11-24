@extends('layouts.app_custom')

@section('content')

<style>
    /* Estilos específicos del dashboard */
    .welcome-header {
        margin-bottom: 30px;
    }
    
    .module-card {
        text-decoration: none;
        color: inherit;
        transition: transform 0.3s, border-color 0.3s;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: space-between;
        min-height: 160px;
        border: 2px solid transparent;
        cursor: pointer;
    }

    .module-card:hover {
        transform: translateY(-5px);
        border-color: var(--accent-soft);
    }

    .module-icon {
        font-size: 2.5rem;
        padding: 15px;
        border-radius: 15px;
        margin-bottom: 15px;
    }

    /* Colores específicos por módulo */
    .mod-workout .module-icon { background: #fee2e2; color: #ef4444; } /* Rojo suave */
    .mod-sleep .module-icon { background: #e0e7ff; color: #6366f1; }   /* Azul suave */
    .mod-diet .module-icon { background: #dcfce7; color: #22c55e; }    /* Verde suave */

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-head);
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: var(--text-body);
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: white;
        border-radius: 20px;
        border: 2px dashed var(--border-color);
    }
</style>

<div class="welcome-header">
    <h2>Panel Principal</h2>
    <p>Aquí tienes un resumen de tu progreso actual.</p>
</div>

<!-- SECCIÓN 1: MÓDULOS PRINCIPALES -->
<div class="grid-3" style="margin-bottom: 40px;">
    <!-- Rutinas -->
    <a href="{{ route('routines.index') }}" class="card module-card mod-workout">
        <div class="module-icon"><i class="bi bi-activity"></i></div>
        <div>
            <h4>Entrenamiento</h4>
            <span style="font-size: 0.9rem; color: var(--text-body)">Gestionar rutinas y registrar sesiones</span>
        </div>
    </a>

    <!-- Sueño -->
    <a href="{{ route('sleep.index') }}" class="card module-card mod-sleep">
        <div class="module-icon"><i class="bi bi-moon-stars"></i></div>
        <div>
            <h4>Descanso</h4>
            <span style="font-size: 0.9rem; color: var(--text-body)">Registrar horas de sueño y calidad</span>
        </div>
    </a>

    <!-- Dieta -->
    <a href="{{ route('diet.index') }}" class="card module-card mod-diet">
        <div class="module-icon"><i class="bi bi-egg-fried"></i></div>
        <div>
            <h4>Nutrición</h4>
            <span style="font-size: 0.9rem; color: var(--text-body)">Diario de comidas y análisis IA</span>
        </div>
    </a>
</div>

<!-- SECCIÓN 2: ESTADÍSTICAS RÁPIDAS -->
<h3 style="margin-bottom: 20px;">Tus Estadísticas</h3>

@if($isNewUser)
    <!-- ESTADO VACÍO (Nuevo Usuario) -->
    <div class="empty-state card">
        <i class="bi bi-clipboard-data" style="font-size: 3rem; color: var(--accent-soft); margin-bottom: 15px;"></i>
        <h3>¡Aún no hay datos!</h3>
        <p>Parece que eres nuevo por aquí. Comienza registrando tu primera rutina, comida o noche de descanso arriba.</p>
    </div>
@else
    <!-- GRILLA DE DATOS -->
    <div class="grid-4" style="margin-bottom: 30px;">
        <!-- Racha -->
        <div class="card">
            <div class="stat-label"><i class="bi bi-fire" style="color: orange;"></i> Racha Actual</div>
            <div class="stat-value">{{ $streak }} <span style="font-size: 1rem;">días</span></div>
            <div style="font-size: 0.8rem; color: #aaa">¡Sigue así!</div>
        </div>

        <!-- Entrenamientos Semana -->
        <div class="card">
            <div class="stat-label">Entrenos (Semana)</div>
            <div class="stat-value">{{ $workoutsThisWeek }}</div>
            <div style="font-size: 0.8rem; color: #aaa">Sesiones completadas</div>
        </div>

        <!-- Promedio Sueño -->
        <div class="card">
            <div class="stat-label">Promedio Sueño</div>
            <div class="stat-value">
                {{ $avgSleep ? number_format($avgSleep, 1) : '-' }} 
                <span style="font-size: 1rem;">hrs</span>
            </div>
            <div style="font-size: 0.8rem; color: #aaa">Últimos 7 días</div>
        </div>

        <!-- Calorías Hoy -->
        <div class="card">
            <div class="stat-label">Consumo Hoy</div>
            <div class="stat-value" style="color: var(--primary)">{{ number_format($chartDataValues[6] ?? 0) }}</div>
            <div style="font-size: 0.8rem; color: #aaa">Calorías (kcal)</div>
        </div>
    </div>

    <!-- SECCIÓN 3: GRÁFICAS -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4>Historial de Calorías (Últimos 7 días)</h4>
        </div>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="caloriesChart"></canvas>
        </div>
    </div>

    <!-- Script de Chart.js -->
    <script>
        const ctx = document.getElementById('caloriesChart').getContext('2d');
        const caloriesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Calorías Consumidas',
                    data: {!! json_encode($chartDataValues) !!},
                    borderColor: '#f3ba60', /* Color Primario */
                    backgroundColor: 'rgba(243, 186, 96, 0.2)',
                    borderWidth: 3,
                    tension: 0.4, /* Curvas suaves */
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#f3ba60',
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
@endif

@endsection