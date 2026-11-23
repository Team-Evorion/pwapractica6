@extends('layouts.rooms')

@section('title', $isCustomer ? 'Mis reservas' : 'Panel de reservaciones')

@section('content')
    <section class="catalog-hero">
        <div class="container">
            <p class="eyebrow">{{ $isCustomer ? 'Tu próxima estadía' : 'Recepción del hotel' }}</p>
            <h1>{{ $isCustomer ? 'Mis reservas' : 'Gestión de reservaciones' }}</h1>
            <p class="hero-copy">Consulta disponibilidad por fechas, registra estadías y revisa su estado.</p>
        </div>
    </section>

    <section class="container catalog-section">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('bookings.index') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label" for="filter_check_in">Entrada</label>
                        <input class="form-control" id="filter_check_in" name="check_in_date" type="date" value="{{ $filters['check_in_date'] ?? '' }}" min="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label" for="filter_check_out">Salida</label>
                        <input class="form-control" id="filter_check_out" name="check_out_date" type="date" value="{{ $filters['check_out_date'] ?? '' }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label" for="status">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pendiente</option>
                            <option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>Confirmada</option>
                            <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Cancelada</option>
                        </select>
                    </div>
                    @if (! $isCustomer)
                        <div class="col-12 col-md-2">
                            <label class="form-label" for="customer_id_filter">Cliente</label>
                            <select class="form-select" id="customer_id_filter" name="customer_id">
                                <option value="">Todos</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected((string) ($filters['customer_id'] ?? '') === (string) $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button class="btn btn-hotel flex-grow-1" type="submit">Buscar</button>
                        <a class="btn btn-outline-secondary" href="{{ route('bookings.index') }}">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        @can('create', \App\Models\Booking::class)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 mb-0">{{ $isCustomer ? 'Solicitar una reservación' : 'Registrar reservación para un cliente' }}</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bookings.store') }}" class="row g-3 align-items-end">
                        @csrf
                        @if ($canManage)
                            <div class="col-12 col-lg-3">
                                <label class="form-label" for="user_id">Cliente</label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">Seleccionar</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected((string) old('user_id') === (string) $customer->id)>{{ $customer->name }} · {{ $customer->email }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif
                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="room_id">Habitación disponible</label>
                            <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                <option value="">Seleccionar</option>
                                @foreach ($availableRooms as $room)
                                    <option value="{{ $room->id }}" @selected((string) old('room_id') === (string) $room->id)>#{{ $room->room_number }} · {{ $room->room_type }} · ${{ number_format((float) $room->room_price, 2) }}</option>
                                @endforeach
                            </select>
                            @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6 col-lg-2">
                            <label class="form-label" for="check_in_date">Entrada</label>
                            <input class="form-control @error('check_in_date') is-invalid @enderror" id="check_in_date" name="check_in_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('check_in_date', $filters['check_in_date'] ?? '') }}" required>
                            @error('check_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6 col-lg-2">
                            <label class="form-label" for="check_out_date">Salida</label>
                            <input class="form-control @error('check_out_date') is-invalid @enderror" id="check_out_date" name="check_out_date" type="date" value="{{ old('check_out_date', $filters['check_out_date'] ?? '') }}" required>
                            @error('check_out_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-lg-2">
                            <button class="btn btn-hotel w-100" type="submit">Guardar</button>
                        </div>
                    </form>
                    @if (! empty($filters['check_in_date']) && ! empty($filters['check_out_date']))
                        <p class="small text-muted mt-3 mb-0">{{ $availableRooms->count() }} habitaciones disponibles para el intervalo seleccionado.</p>
                    @endif
                </div>
            </div>
        @endcan

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            @if (! $isCustomer)<th>Cliente</th>@endif
                            <th>Habitación</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            @php
                                $status = match ($booking->status) {
                                    \App\Models\Booking::STATUS_CONFIRMED => ['Confirmada', 'text-bg-success'],
                                    \App\Models\Booking::STATUS_CANCELLED => ['Cancelada', 'text-bg-secondary'],
                                    default => ['Pendiente', 'text-bg-warning'],
                                };
                            @endphp
                            <tr>
                                @if (! $isCustomer)
                                    <td><strong>{{ $booking->user->name }}</strong><span class="d-block small text-muted">{{ $booking->user->email }}</span></td>
                                @endif
                                <td>#{{ $booking->room->room_number }}<span class="d-block small text-muted">{{ $booking->room->room_type }}</span></td>
                                <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                                <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $status[1] }}">{{ $status[0] }}</span></td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                        <a class="btn btn-sm btn-outline-hotel" href="{{ route('rooms.show', $booking->room) }}">Habitación</a>
                                        @can('update', $booking)
                                            @if ($canManage && $booking->status === \App\Models\Booking::STATUS_PENDING)
                                                <form method="POST" action="{{ route('bookings.status.update', $booking) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="confirmed"><button class="btn btn-sm btn-success" type="submit">Confirmar</button></form>
                                            @endif
                                            @if ($booking->status !== \App\Models\Booking::STATUS_CANCELLED)
                                                <form method="POST" action="{{ route('bookings.status.update', $booking) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="cancelled"><button class="btn btn-sm btn-outline-danger" type="submit">Cancelar</button></form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $isCustomer ? 5 : 6 }}" class="text-center text-muted py-5">No hay reservaciones con esos criterios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($bookings->hasPages())<div class="card-footer bg-white">{{ $bookings->links() }}</div>@endif
        </div>
    </section>
@endsection
