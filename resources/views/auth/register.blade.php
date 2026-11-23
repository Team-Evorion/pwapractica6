@extends('layouts.app')

@section('title', 'Registro de Huésped | Sistema de Reservas de Hotel')

@section('content')
<div class="row justify-content-center align-items-center py-4">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-bold mb-1">Crear cuenta de huésped</h1>
                <p class="text-muted small mb-4">El registro público crea automáticamente una cuenta con rol <strong>Cliente</strong>.</p>

                <form method="POST" action="{{ route('register.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Nombre completo</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required
                            autofocus
                            autocomplete="name"
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required
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
                            autocomplete="new-password"
                        >
                        <div class="form-text">Mínimo 8 caracteres, incluyendo letras y números.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-medium">Confirmar contraseña</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-hotel fw-semibold py-2">
                            Registrarme
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center small mb-0">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="text-hotel-teal fw-semibold text-decoration-none">Inicia sesión aquí</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
