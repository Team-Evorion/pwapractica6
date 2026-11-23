@extends('layouts.app')

@section('title', 'Crear Usuario | Administración')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Registrar Nuevo Usuario</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Volver al listado</a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Nombre completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="role_id" class="form-label fw-medium">Rol asignado</label>
                            <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">Selecciona un rol...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected((string) old('role_id') === (string) $role->id)>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 d-flex align-items-center pt-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">Cuenta activa</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label fw-medium">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            <div class="form-text">Mínimo 8 caracteres, letras y números.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="password_confirmation" class="form-label fw-medium">Confirmar contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-hotel fw-semibold">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
