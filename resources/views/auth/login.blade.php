@extends('layouts.rooms')

@section('title', 'Iniciar sesión')

@section('content')
    <section class="container detail-section">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="detail-panel">
                    <p class="eyebrow">Acceso de huéspedes</p>
                    <h1>Iniciar sesión</h1>
                    <p class="detail-description">Usa el correo y la contraseña de tu usuario de Hotel Evorion.</p>

                    <form method="POST" action="{{ route('login.store') }}" class="mt-4">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="email">Correo electrónico</label>
                            <input
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="password">Contraseña</label>
                            <input
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-hotel w-100" type="submit">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
