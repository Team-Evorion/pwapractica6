@extends('layouts.app')

@section('title', 'Gestión de Usuarios | Administración')

@push('styles')
<style>{!! file_get_contents(resource_path('css/modules/administration.css')) !!}</style>
@endpush

@section('content')
<div class="admin-header-bar rounded-3 shadow-sm p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Gestión de Usuarios y Roles</h1>
        <p class="text-muted small mb-0">Administra cuentas del personal, huéspedes y proveedores del hotel.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-hotel fw-semibold">
        + Nuevo Usuario
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label for="search" class="form-label small fw-medium">Buscar por nombre o correo</label>
                <input type="text" id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Ej. Ana o ana@hotel.com">
            </div>
            <div class="col-12 col-md-3">
                <label for="role_id" class="form-label small fw-medium">Filtrar por rol</label>
                <select id="role_id" name="role_id" class="form-select">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) $roleFilter === (string) $role->id)>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="status" class="form-label small fw-medium">Estado</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" @selected($statusFilter === 'active')>Activos</option>
                    <option value="inactive" @selected($statusFilter === 'inactive')>Desactivados</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $item)
                    @php
                        $slug = \Illuminate\Support\Str::slug($item->role?->name ?? 'customer');
                    @endphp
                    <tr>
                        <td class="fw-semibold">#{{ $item->id }}</td>
                        <td>
                            <div class="fw-medium">{{ $item->name }}</div>
                            @if (auth()->id() === $item->id)
                                <span class="badge bg-info text-dark">Tu cuenta</span>
                            @endif
                        </td>
                        <td>{{ $item->email }}</td>
                        <td>
                            <span class="badge role-pill role-pill-{{ $slug }}">
                                {{ $item->role?->name ?? 'Sin rol' }}
                            </span>
                        </td>
                        <td>
                            @if ($item->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Desactivado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                <a href="{{ route('admin.users.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                @can('toggleActive', $item)
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $item) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            {{ $item->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                @endcan

                                @can('delete', $item)
                                    <form method="POST" action="{{ route('admin.users.destroy', $item) }}" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar al usuario {{ addslashes($item->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Eliminar
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            No se encontraron usuarios con los criterios seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection