@extends('layouts.rooms')

@section('title', 'Habitación '.$room->room_number)

@section('content')
    <section class="container detail-section">
        <a class="back-link" href="{{ route('rooms.index') }}">← Volver al catálogo</a>
        <div class="row g-4 g-lg-5 align-items-stretch">
            <div class="col-12 col-lg-7">
                @if ($room->image_path)
                    <img class="detail-image" src="{{ asset($room->image_path) }}" alt="Habitación {{ $room->room_number }}">
                @else
                    <div class="detail-image room-placeholder room-placeholder-{{ strtolower($room->room_type) }}">
                        <span>{{ $room->room_type }}</span>
                    </div>
                @endif
            </div>
            <div class="col-12 col-lg-5">
                <div class="detail-panel">
                    <p class="eyebrow">Habitación {{ $room->room_number }}</p>
                    <h1>{{ $room->room_type }}</h1>
                    <p class="detail-description">{{ $room->description ?: 'Una habitación preparada para una estancia cómoda y tranquila.' }}</p>
                    <dl class="room-facts">
                        <div>
                            <dt>Capacidad</dt>
                            <dd>{{ $room->capacity }} {{ $room->capacity === 1 ? 'huésped' : 'huéspedes' }}</dd>
                        </div>
                        <div>
                            <dt>Estado</dt>
                            <dd>{{ $room->is_available ? 'Disponible' : 'Fuera de servicio' }}</dd>
                        </div>
                    </dl>
                    <p class="detail-price"><strong>${{ number_format((float) $room->room_price, 2) }}</strong><span> por noche</span></p>
                    <p class="integration-note">La selección por fechas se habilitará al integrar Reservaciones.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
