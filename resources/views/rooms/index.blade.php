@extends('layouts.rooms')

@section('title', 'Habitaciones')

@section('content')
    <section class="catalog-hero">
        <div class="container">
            <p class="eyebrow">Descanso pensado para cada viaje</p>
            <h1>Encuentra tu habitación</h1>
            <p class="hero-copy">Explora opciones disponibles y ajusta el precio a tu estadía.</p>
        </div>
    </section>

    <section class="container catalog-section">
        <form class="filter-panel" method="GET" action="{{ route('rooms.index') }}" data-price-filter>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="type">Tipo</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Todos los tipos</option>
                        @foreach ($roomTypes as $type)
                            <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="min_price">Precio mínimo</label>
                    <input class="form-control" id="min_price" name="min_price" type="number" min="0" step="0.01" value="{{ $filters['min_price'] ?? '' }}" placeholder="$0">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="max_price">Precio máximo</label>
                    <input class="form-control" id="max_price" name="max_price" type="number" min="0" step="0.01" value="{{ $filters['max_price'] ?? '' }}" placeholder="$300">
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-hotel" type="submit">Buscar</button>
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ route('rooms.index') }}">Limpiar filtros</a>
                <span class="text-danger small" data-price-error role="alert"></span>
            </div>
        </form>

        @if ($rooms->isEmpty())
            <div class="empty-state">
                <h2>No encontramos habitaciones</h2>
                <p>Prueba otro tipo o modifica el rango de precios.</p>
                <a class="btn btn-outline-hotel" href="{{ route('rooms.index') }}">Ver todas</a>
            </div>
        @else
            <div class="row g-4">
                @foreach ($rooms as $room)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="room-card h-100">
                            @if ($room->image_path)
                                <img class="room-image" src="{{ asset($room->image_path) }}" alt="Habitación {{ $room->room_number }}">
                            @else
                                <div class="room-image room-placeholder room-placeholder-{{ strtolower($room->room_type) }}">
                                    <span>{{ $room->room_type }}</span>
                                </div>
                            @endif
                            <div class="room-card-body">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <p class="room-number">Habitación {{ $room->room_number }}</p>
                                        <h2>{{ $room->room_type }}</h2>
                                    </div>
                                    <span class="capacity">{{ $room->capacity }} {{ $room->capacity === 1 ? 'huésped' : 'huéspedes' }}</span>
                                </div>
                                <p class="room-description">{{ $room->description ?: 'Una opción cómoda para disfrutar tu estancia.' }}</p>
                                <div class="room-card-footer">
                                    <p class="price"><strong>${{ number_format((float) $room->room_price, 2) }}</strong><span> / noche</span></p>
                                    <a class="btn btn-outline-hotel" href="{{ route('rooms.show', $room) }}">Ver detalles</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                {{ $rooms->links() }}
            </div>
        @endif
    </section>
@endsection
