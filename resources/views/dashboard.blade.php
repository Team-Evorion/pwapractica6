@extends('layouts.app')

@section('title', 'Panel Principal | Sistema de Reservas de Hotel')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <h1 class="h4 fw-bold mb-2">Panel General</h1>
        <p class="mb-1">Usuario autenticado: <strong>{{ auth()->user()->name }}</strong></p>
        <p class="mb-3">Rol asignado: <span class="badge bg-dark">{{ auth()->user()->role?->name }}</span></p>
        <div class="alert alert-info mb-0">
            Vista base de aterrizaje tras autenticación (Etapa 2). En las siguientes etapas se habilitarán los paneles específicos de Administración, Proveedores y las integraciones de Habitaciones/Reservaciones.
        </div>
    </div>
</div>
@endsection