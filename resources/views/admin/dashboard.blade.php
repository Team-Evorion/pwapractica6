@extends('layouts.app')

@section('title', 'Panel de Administración | Sistema de Reservas de Hotel')

@push('styles')
<style>{!! file_get_contents(resource_path('css/modules/administration.css')) !!}</style>
@endpush

@section('content')
<div class="admin-header-bar rounded-3 shadow-sm p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Panel de Administración</h1>
        <p class="text-muted small mb-0">Supervisión general de usuarios, roles, suministros y operaciones del hotel.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-dark btn-sm fw-semibold">Gestionar Usuarios</a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-hotel btn-sm fw-semibold">+ Nuevo Usuario</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="dashboard-stat-top"><div class="text-muted small fw-medium">Usuarios Registrados</div><span class="dashboard-stat-icon"><i class="bi bi-people"></i></span></div>
                <div class="h3 fw-bold mt-1 mb-0">{{ $totalUsers }}</div>
                <div class="small text-hotel-teal mt-2">{{ $activeUsers }} activos &middot; {{ $inactiveUsers }} inactivos</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="dashboard-stat-top"><div class="text-muted small fw-medium">Habitaciones Operativas</div><span class="dashboard-stat-icon"><i class="bi bi-door-open"></i></span></div>
                <div class="h3 fw-bold mt-1 mb-0">{{ $hotelSummary['available_rooms'] }} / {{ $hotelSummary['total_rooms'] }}</div>
                <div class="small text-muted mt-2">{{ $hotelSummary['maintenance_rooms'] }} fuera de servicio</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="dashboard-stat-top"><div class="text-muted small fw-medium">Reservaciones Activas</div><span class="dashboard-stat-icon"><i class="bi bi-calendar2-check"></i></span></div>
                <div class="h3 fw-bold mt-1 mb-0">{{ $hotelSummary['active_reservations'] }}</div>
                <div class="small text-muted mt-2">{{ $hotelSummary['checkins_today'] }} check-ins hoy &middot; {{ $hotelSummary['checkouts_today'] }} check-outs</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="dashboard-stat-top"><div class="text-muted small fw-medium">Registros en Suministros</div><span class="dashboard-stat-icon"><i class="bi bi-box-seam"></i></span></div>
                <div class="h3 fw-bold mt-1 mb-0">{{ $totalSupplies }}</div>
                <div class="small text-muted mt-2">Necesidades y ofertas registradas</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h6 fw-bold mb-0">Usuarios por Rol</h2>
            </div>
            <div class="card-body">
                @forelse ($roleDistribution as $roleItem)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span class="fw-medium">{{ $roleItem->display_name }}</span>
                        <span class="badge bg-dark rounded-pill">{{ $roleItem->users_count }}</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No hay roles registrados.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h2 class="h6 fw-bold mb-0">Usuarios Recientes</h2>
                <a href="{{ route('admin.users.index') }}" class="small text-hotel-teal fw-semibold text-decoration-none">Ver todos</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentUsers as $recent)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $recent->name }}</div>
                                    <div class="small text-muted">{{ $recent->email }}</div>
                                </td>
                                <td><span class="badge bg-secondary">{{ $recent->role?->display_name ?? 'Sin rol' }}</span></td>
                                <td>
                                    @if ($recent->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Sin usuarios recientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
