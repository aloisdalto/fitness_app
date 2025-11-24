@extends('layouts.guest_custom')

@section('content')
<style>
    .hero-section {
        text-align: center;
        max-width: 800px;
    }
    .hero-title {
        font-size: 3.5rem;
        line-height: 1.2;
        margin-bottom: 20px;
        color: var(--text-head);
    }
    .hero-subtitle {
        font-size: 1.2rem;
        color: var(--text-body);
        margin-bottom: 40px;
    }
    .action-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    /* Forma decorativa de fondo */
    .bg-shape {
        position: fixed;
        top: -10%;
        right: -10%;
        width: 50%;
        height: 80%;
        background-color: var(--accent-soft);
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        z-index: -1;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .hero-title { font-size: 2.5rem; }
    }
</style>

<div class="bg-shape"></div>

<div class="hero-section">
    <div class="logo-text">Wellness<span class="logo-span">Tracker</span></div>
    
    <h1 class="hero-title">Domina tu Rutina, <br> Transforma tu Vida</h1>
    
    <p class="hero-subtitle">
        La herramienta definitiva para monitorear tus entrenamientos, 
        optimizar tu descanso y equilibrar tu nutrición en un solo lugar.
    </p>

    <div class="action-buttons">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Ir al Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Iniciar Sesión</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-outline">Crear Cuenta</a>
                @endif
            @endauth
        @endif
    </div>
</div>
@endsection