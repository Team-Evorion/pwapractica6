@extends('layouts.app')

@section('title', 'Iniciar Sesión | Sistema de Reservas de Hotel')

@section('content')
<div class="row justify-content-center align-items-center py-4">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-bold mb-1">Iniciar sesión</h1>
                <p class="text-muted small mb-4">Accede con tus credenciales al sistema hotelero.</p>

                <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required
                            autofocus
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Contraseña</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required
                            autocomplete="current-password"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="remember">Mantener sesión iniciada</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-hotel fw-semibold py-2">
                            Ingresar
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center small mb-0">
                    ¿No tienes una cuenta?
                    <a href="{{ route('register') }}" class="text-hotel-teal fw-semibold text-decoration-none">Regístrate como huésped</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection