@extends('layouts.guest_custom')

@section('content')
<div class="auth-card" style="max-width: 600px;">
    <div class="auth-header">
        <a href="{{ url('/') }}" class="logo-text">Wellness<span class="logo-span">Tracker</span></a>
        <h3>Crea tu cuenta</h3>
        <p style="font-size: 0.9rem;">Comienza tu viaje hoy mismo</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nombre -->
        <div class="form-group">
            <label for="name" class="form-label">Nombre Completo</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <!-- Datos Físicos (Grid de 2 columnas) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="age" class="form-label">Edad</label>
                <input id="age" type="number" class="form-control" name="age" required>
            </div>
            <div class="form-group">
                <label for="gender" class="form-label">Sexo</label>
                <select id="gender" class="form-control" name="gender" required>
                    <option value="male">Hombre</option>
                    <option value="female">Mujer</option>
                    <option value="other">Otro</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="height_cm" class="form-label">Altura (cm)</label>
                <input id="height_cm" type="number" class="form-control" name="height_cm" required>
            </div>
            <div class="form-group">
                <label for="weight_kg" class="form-label">Peso (kg)</label>
                <input id="weight_kg" type="number" step="0.1" class="form-control" name="weight_kg" required>
            </div>
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
            Registrarse
        </button>

        <div style="text-align: center; margin-top: 20px;">
            <p style="font-size: 0.9rem;">¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;">Ingresa aquí</a></p>
            <br>
            <a href="{{ url('/') }}" class="btn-link">← Volver al inicio</a>
        </div>
    </form>
</div>
@endsection