@extends('layouts.app_custom')

@section('content')

<!-- HEADER & ACTIONS -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2>Monitor de Sueño</h2>
        <p style="color: var(--text-body);">Registra y optimiza tu descanso diario</p>
    </div>
    <button onclick="openSleepModal()" class="btn-primary" style="border: none; padding: 12px 25px; border-radius: 50px; font-weight: 600; cursor: pointer;">
        + Registrar Noche
    </button>
</div>

<!-- STATS CARDS -->
<div class="grid-3" style="margin-bottom: 30px;">
    <!-- Promedio Horas -->
    <div class="card" style="display: flex; align-items: center; gap: 20px;">
        <div style="background: #e0e7ff; color: #6366f1; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="bi bi-clock-history"></i>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-head);">
                {{ number_format($avgDuration ?? 0, 1) }}h
            </div>
            <div style="font-size: 0.9rem; color: #888;">Promedio Horas</div>
        </div>
    </div>

    <!-- Calidad Promedio -->
    <div class="card" style="display: flex; align-items: center; gap: 20px;">
        <div style="background: #fef3c7; color: #d97706; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="bi bi-star-fill"></i>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-head);">
                {{ number_format($avgQuality ?? 0, 1) }}
            </div>
            <div style="font-size: 0.9rem; color: #888;">Calidad Promedio (1-5)</div>
        </div>
    </div>

    <!-- Mejor Noche -->
    <div class="card" style="display: flex; align-items: center; gap: 20px;">
        <div style="background: #dcfce7; color: #22c55e; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="bi bi-trophy"></i>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-head);">
                {{ number_format($bestNight ?? 0, 1) }}h
            </div>
            <div style="font-size: 0.9rem; color: #888;">Mejor Noche</div>
        </div>
    </div>
</div>

<!-- CHART SECTION -->
<div class="card" style="margin-bottom: 30px;">
    <h4 style="margin-bottom: 20px;">Tendencia de Sueño (Últimos 7 registros)</h4>
    <div style="position: relative; height: 300px; width: 100%;">
        <canvas id="sleepChart"></canvas>
    </div>
</div>

<!-- HISTORIAL LIST -->
<h3 style="margin-bottom: 20px;">Historial de Descanso</h3>
<div class="card" style="padding: 0; overflow: hidden;">
    @if($logs->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f9fafb; border-bottom: 1px solid #eee;">
                <tr>
                    <th style="text-align: left; padding: 15px 20px; color: #666; font-weight: 600;">Fecha (Dormir)</th>
                    <th style="text-align: left; padding: 15px 20px; color: #666; font-weight: 600;">Horario</th>
                    <th style="text-align: left; padding: 15px 20px; color: #666; font-weight: 600;">Duración</th>
                    <th style="text-align: center; padding: 15px 20px; color: #666; font-weight: 600;">Calidad</th>
                    <th style="text-align: right; padding: 15px 20px; color: #666; font-weight: 600;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px 20px; color: var(--text-head);">
                        {{ \Carbon\Carbon::parse($log->bed_time)->format('d M, Y') }}
                    </td>
                    <td style="padding: 15px 20px; font-size: 0.9rem; color: #666;">
                        {{ \Carbon\Carbon::parse($log->bed_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($log->wake_time)->format('H:i') }}
                    </td>
                    <td style="padding: 15px 20px; font-weight: 600; color: var(--primary);">
                        {{ number_format($log->duration_hours, 1) }} hrs
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star-fill" style="font-size: 0.8rem; color: {{ $i <= $log->quality_rating ? '#f3ba60' : '#ddd' }}"></i>
                        @endfor
                    </td>
                    <td style="padding: 15px 20px; text-align: right;">
                        <form action="{{ route('sleep.destroy', $log->id) }}" method="POST" onsubmit="return confirm('¿Borrar registro?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px;">
            <p style="color: #aaa;">No hay registros de sueño aún.</p>
        </div>
    @endif
</div>

<!-- MODAL REGISTRO -->
<div id="sleepModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 450px; padding: 30px; border-radius: 20px; position: relative;">
        <h3 style="margin-bottom: 20px;">Registrar Descanso</h3>
        
        <form action="{{ route('sleep.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Hora de acostarse</label>
                <input type="datetime-local" name="bed_time" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 10px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Hora de levantarse</label>
                <input type="datetime-local" name="wake_time" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 10px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 500;">Calidad del sueño (1-5)</label>
                <div style="display: flex; justify-content: space-between; gap: 10px;">
                    @for($i=1; $i<=5; $i++)
                        <label style="cursor: pointer; flex: 1;">
                            <input type="radio" name="quality_rating" value="{{ $i }}" style="display: none;" class="star-radio">
                            <div class="star-box" style="border: 1px solid #ddd; padding: 10px; text-align: center; border-radius: 8px; transition: 0.2s;">
                                {{ $i }} <i class="bi bi-star-fill" style="font-size: 0.8rem;"></i>
                            </div>
                        </label>
                    @endfor
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; border: none; border-radius: 50px; cursor: pointer; font-weight: 600;">Guardar Registro</button>
        </form>
        
        <button onclick="document.getElementById('sleepModal').style.display='none'" style="position: absolute; top: 15px; right: 15px; border: none; background: none; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>
</div>

<style>
    /* Estilos extra para el selector de estrellas */
    .star-radio:checked + .star-box {
        background-color: var(--primary);
        color: var(--text-head);
        border-color: var(--primary);
    }
    .star-box:hover {
        background-color: #f9f9f9;
    }
</style>

<script>
    function openSleepModal() {
        document.getElementById('sleepModal').style.display = 'flex';
    }

    // Chart JS Config
    const ctx = document.getElementById('sleepChart').getContext('2d');
    const sleepChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Horas de Sueño',
                data: {!! json_encode($chartValues) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false, // Para que la gráfica se centre mejor en los valores reales
                    suggestedMin: 4,
                    suggestedMax: 10,
                    grid: { color: '#f0f0f0' }
                },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

@endsection