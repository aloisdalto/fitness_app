@extends('layouts.guest_custom')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <a href="{{ url('/') }}" class="logo-text">Wellness<span class="logo-span">Tracker</span></a>
        <h3>¡Bienvenido de nuevo!</h3>
        <p style="font-size: 0.9rem;">Ingresa tus credenciales para continuar</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="ejemplo@correo.com">
            @error('email')
                <span style="color: red; font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <span style="color: red; font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Botones y Links -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <label style="font-size: 0.9rem; cursor: pointer;">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                Recordarme
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            Entrar
        </button>

        <div style="text-align: center; margin-top: 20px;">
            <p style="font-size: 0.9rem;">¿No tienes cuenta? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Regístrate aquí</a></p>
            <br>
            <a href="{{ url('/') }}" class="btn-link">← Volver al inicio</a>
        </div>
    </form>
</div>
@endsection