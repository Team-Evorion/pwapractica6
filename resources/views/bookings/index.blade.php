@extends('layouts.rooms')

@section('title', 'Mis reservas')

@section('content')
    <section class="catalog-hero">
        <div class="container">
            <p class="eyebrow">Tu próxima estadía</p>
            <h1>Mis reservas</h1>
            <p class="hero-copy">Consulta las habitaciones que reservaste y el estado de cada solicitud.</p>
        </div>
    </section>

    <section class="container catalog-section">
        @if ($bookings->isEmpty())
            <div class="empty-state">
                <h2>Aún no tienes reservas</h2>
                <p>Explora el catálogo y elige una habitación para tu próxima estadía.</p>
                <a class="btn btn-hotel" href="{{ route('rooms.index') }}">Ver habitaciones</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white shadow-sm">
                    <thead>
                        <tr>
                            <th scope="col">Habitación</th>
                            <th scope="col">Entrada</th>
                            <th scope="col">Salida</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Registrada</th>
                            <th scope="col" class="text-end">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            @php
                                $status = match ($booking->status) {
                                    \App\Models\Booking::STATUS_CONFIRMED => ['Confirmada', 'text-bg-success'],
                                    \App\Models\Booking::STATUS_CANCELLED => ['Cancelada', 'text-bg-secondary'],
                                    default => ['Pendiente', 'text-bg-warning'],
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $booking->room->room_number }}</strong>
                                    <span class="d-block text-muted small">{{ $booking->room->room_type }}</span>
                                </td>
                                <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                                <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $status[1] }}">{{ $status[0] }}</span></td>
                                <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-hotel" href="{{ route('rooms.show', $booking->room) }}">Ver habitación</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
