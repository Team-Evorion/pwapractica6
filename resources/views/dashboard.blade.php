@extends('layouts.app')

@section('title', 'Panel General | Hotel Evorion')

@push('styles')
<style>{!! file_get_contents(resource_path('css/modules/dashboard.css')) !!}</style>
@endpush

@section('content')
<section class="dashboard-hero mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="dashboard-eyebrow">Panel general · {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</span>
            <h1>Hola, {{ $user->name }}.</h1>
            <p>Todo lo necesario para gestionar tu experiencia en Hotel Evorion desde un solo lugar.</p>
            <span class="role-badge">{{ $user->role?->display_name ?? 'Sin rol asignado' }}</span>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="hotel-mark" aria-hidden="true">E</div>
        </div>
    </div>
</section>

<section class="row g-3 mb-4" aria-label="Resumen del hotel">
    <div class="col-12 col-sm-6 col-xl-3">
        <article class="summary-card">
            <span class="summary-icon">01</span>
            <p>Habitaciones</p>
            <strong>{{ $totalRooms }}</strong>
            <small>{{ $availableRooms }} operativas actualmente</small>
        </article>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <article class="summary-card">
            <span class="summary-icon">02</span>
            <p>Reservaciones activas</p>
            <strong>{{ $activeBookings }}</strong>
            <small>{{ $user->hasRole(\App\Models\Role::CUSTOMER) ? 'Asociadas a tu cuenta' : 'Pendientes o confirmadas' }}</small>
        </article>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <article class="summary-card">
            <span class="summary-icon">03</span>
            <p>Necesidades abiertas</p>
            <strong>{{ $openNeeds }}</strong>
            <small>Solicitudes vigentes del hotel</small>
        </article>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <article class="summary-card summary-card-accent">
            <span class="summary-icon">04</span>
            <p>Estado del sistema</p>
            <strong>Activo</strong>
            <small>Módulos conectados y disponibles</small>
        </article>
    </div>
</section>

<div class="row g-4">
    <div class="col-12 col-lg-7">
        <section class="dashboard-panel h-100">
            <div class="panel-heading">
                <div>
                    <span class="dashboard-eyebrow text-hotel-teal">Accesos principales</span>
                    <h2>¿Qué deseas hacer?</h2>
                </div>
            </div>
            <div class="action-grid">
                <a href="{{ route('rooms.index') }}" class="action-card">
                    <span>Habitaciones</span>
                    <strong>Explorar catálogo</strong>
                    <small>Consulta tipos, capacidad y precios por noche.</small>
                </a>

                @can('viewAny', \App\Models\Booking::class)
                    <a href="{{ route('bookings.index') }}" class="action-card">
                        <span>Reservaciones</span>
                        <strong>{{ $user->hasRole(\App\Models\Role::CUSTOMER) ? 'Gestionar mis reservas' : 'Abrir panel de recepción' }}</strong>
                        <small>Disponibilidad por fechas y control de estados.</small>
                    </a>
                @endcan

                @if ($user->hasRole(\App\Models\Role::ADMINISTRATOR))
                    <a href="{{ route('admin.dashboard') }}" class="action-card">
                        <span>Administración</span>
                        <strong>Supervisar el hotel</strong>
                        <small>Usuarios, roles, habitaciones y actividad general.</small>
                    </a>
                @endif

                @can('viewFinances', \App\Models\Booking::class)
                    <a href="{{ route('finances.index') }}" class="action-card">
                        <span>Finanzas</span>
                        <strong>Revisar ingresos y pagos</strong>
                        <small>Ventas confirmadas, saldos, cobros y ocupación.</small>
                    </a>
                @endcan

                @if ($user->hasRole(\App\Models\Role::SUPPLIER))
                    <a href="{{ route('suppliers.dashboard') }}" class="action-card">
                        <span>Proveedores</span>
                        <strong>Revisar necesidades</strong>
                        <small>{{ $supplierOffers }} ofertas registradas por tu cuenta.</small>
                    </a>
                @endif

                @can('create', \App\Models\Room::class)
                    <a href="{{ route('rooms.manage') }}" class="action-card">
                        <span>Inventario</span>
                        <strong>Gestionar habitaciones</strong>
                        <small>Actualiza precios, fotografías y disponibilidad.</small>
                    </a>
                @endcan
            </div>
        </section>
    </div>

    <div class="col-12 col-lg-5">
        <aside class="dashboard-panel h-100">
            <span class="dashboard-eyebrow text-hotel-teal">Próxima actividad</span>
            @if ($nextBooking)
                <h2>Habitación {{ $nextBooking->room?->room_number }}</h2>
                <div class="booking-highlight">
                    <p><span>Entrada</span><strong>{{ $nextBooking->check_in_date->format('d/m/Y') }}</strong></p>
                    <p><span>Salida</span><strong>{{ $nextBooking->check_out_date->format('d/m/Y') }}</strong></p>
                    <p><span>Estado</span><strong>{{ $nextBooking->status === 'confirmed' ? 'Confirmada' : 'Pendiente' }}</strong></p>
                </div>
                <a href="{{ route('bookings.index') }}" class="btn btn-hotel w-100">Ver reservaciones</a>
            @else
                <div class="empty-activity">
                    <div class="empty-symbol">✓</div>
                    <h2>Todo está al día</h2>
                    <p>No hay una próxima reservación activa registrada para mostrar.</p>
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-dark w-100">Ver habitaciones</a>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
