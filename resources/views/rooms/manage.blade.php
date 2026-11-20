@extends('layouts.rooms')

@section('title', 'Administrar habitaciones')

@section('content')
    <section class="container management-section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Gestión del hotel</p>
                <h1>Habitaciones</h1>
            </div>
            <a class="btn btn-hotel" href="{{ route('rooms.create') }}">Nueva habitación</a>
        </div>

        <form class="filter-panel compact" method="GET" action="{{ route('rooms.manage') }}" data-price-filter>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="type">Tipo</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Todos</option>
                        @foreach ($roomTypes as $type)
                            <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="min_price">Desde</label>
                    <input class="form-control" id="min_price" name="min_price" type="number" min="0" step="0.01" value="{{ $filters['min_price'] ?? '' }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="max_price">Hasta</label>
                    <input class="form-control" id="max_price" name="max_price" type="number" min="0" step="0.01" value="{{ $filters['max_price'] ?? '' }}">
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-dark" type="submit">Filtrar</button>
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ route('rooms.manage') }}">Limpiar</a>
                <span class="text-danger small" data-price-error role="alert"></span>
            </div>
        </form>

        <div class="table-responsive management-table">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Tipo</th>
                        <th>Capacidad</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td class="fw-semibold">{{ $room->room_number }}</td>
                            <td>{{ $room->room_type }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td>${{ number_format((float) $room->room_price, 2) }}</td>
                            <td>
                                <span class="status-badge {{ $room->is_available ? 'available' : 'unavailable' }}">
                                    {{ $room->is_available ? 'Disponible' : 'Fuera de servicio' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rooms.show', $room) }}">Ver</a>
                                    <a class="btn btn-sm btn-outline-hotel" href="{{ route('rooms.edit', $room) }}">Editar</a>
                                    <form method="POST" action="{{ route('rooms.destroy', $room) }}" data-delete-room>
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No hay habitaciones para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rooms->links() }}
        </div>
    </section>
@endsection
