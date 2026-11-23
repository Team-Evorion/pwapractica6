@extends('layouts.rooms')

@section('title', 'Una estancia para recordar')

@push('styles')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="home-hero" style="--hero-image: url('{{ asset('images/rooms/room-suite.webp') }}')">
    <div class="container home-hero-content">
        <p class="home-kicker">Hospitalidad contemporánea · Ecuador</p>
        <h1>Donde cada estancia se siente extraordinaria.</h1>
        <p class="home-intro">Habitaciones luminosas, atención cercana y la tranquilidad de reservar directamente con nosotros.</p>
        <div class="d-flex flex-column flex-sm-row gap-3">
            <a class="btn btn-home-primary" href="{{ route('rooms.index') }}">Descubrir habitaciones</a>
            @guest
                <a class="btn btn-home-ghost" href="{{ route('register') }}">Crear una cuenta</a>
            @else
                <a class="btn btn-home-ghost" href="{{ route('dashboard') }}">Ir a mi panel</a>
            @endguest
        </div>
    </div>
    <div class="hero-scroll-note">Desliza para explorar <span aria-hidden="true">↓</span></div>
</section>

<section class="container booking-ribbon" aria-label="Buscar habitaciones">
    <form method="GET" action="{{ route('rooms.index') }}" class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label for="home-type" class="form-label">Tipo de habitación</label>
            <select id="home-type" name="type" class="form-select">
                <option value="">Todas las opciones</option>
                @foreach (\App\Models\Room::TYPES as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="home-min-price" class="form-label">Desde</label>
            <input id="home-min-price" name="min_price" type="number" min="0" step="1" class="form-control" placeholder="$45">
        </div>
        <div class="col-6 col-md-3">
            <label for="home-max-price" class="form-label">Hasta</label>
            <input id="home-max-price" name="max_price" type="number" min="0" step="1" class="form-control" placeholder="$240">
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button class="btn btn-hotel" type="submit">Consultar</button>
        </div>
    </form>
</section>

<section class="home-section home-welcome">
    <div class="container">
        <div class="row align-items-end g-4 mb-5">
            <div class="col-lg-5">
                <p class="home-kicker text-teal">Bienvenido a Evorion</p>
                <h2>Una pausa elegante en medio de tu viaje.</h2>
            </div>
            <div class="col-lg-6 offset-lg-1">
                <p class="section-lead">Creamos una experiencia serena y sencilla: espacios cuidados, tarifas claras y una reserva que puedes gestionar a tu ritmo.</p>
            </div>
        </div>
        <div class="hotel-values">
            <div><strong>8</strong><span>habitaciones seleccionadas</span></div>
            <div><strong>4</strong><span>categorías para cada viaje</span></div>
            <div><strong>24/7</strong><span>gestión digital de reservas</span></div>
            <div><strong>Directa</strong><span>atención sin intermediarios</span></div>
        </div>
    </div>
</section>

<section class="home-section rooms-showcase">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="home-kicker text-teal">Habitaciones y suites</p>
                <h2>Elige tu manera de descansar.</h2>
            </div>
            <a href="{{ route('rooms.index') }}">Ver todo el catálogo <span aria-hidden="true">→</span></a>
        </div>

        <div class="row g-4">
            @forelse ($featuredRooms as $room)
                <div class="col-12 col-md-6 col-xl-4">
                    <article class="editorial-room-card">
                        <a href="{{ route('rooms.show', $room) }}" class="room-photo-link">
                            <img src="{{ asset($room->image_path) }}" alt="Habitación {{ $room->room_number }} de tipo {{ $room->room_type }}">
                            <span>Explorar</span>
                        </a>
                        <div class="editorial-room-copy">
                            <div>
                                <p>Habitación {{ $room->room_number }}</p>
                                <h3>{{ $room->room_type }}</h3>
                            </div>
                            <p class="room-rate">Desde <strong>${{ number_format((float) $room->room_price, 0) }}</strong> / noche</p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="empty-state">Pronto publicaremos nuestras habitaciones disponibles.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="home-section experience-section">
    <div class="container">
        <div class="row g-0 experience-card">
            <div class="col-lg-7">
                <img src="{{ asset('images/rooms/room-doble.webp') }}" alt="Interior cálido de una habitación doble de Hotel Evorion">
            </div>
            <div class="col-lg-5 experience-copy">
                <p class="home-kicker">La experiencia Evorion</p>
                <h2>Calma, comodidad y detalles que sí importan.</h2>
                <p>Desde el primer vistazo hasta el check-out, nuestro sistema conecta habitaciones, recepción y reservas para ofrecerte una estancia organizada y sin complicaciones.</p>
                <ul>
                    <li>Tarifas visibles y sin sorpresas</li>
                    <li>Disponibilidad controlada por fechas</li>
                    <li>Opciones para viajes individuales y familiares</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="home-cta">
    <div class="container text-center">
        <p class="home-kicker">Tu próxima estancia comienza aquí</p>
        <h2>Encuentra un espacio hecho para descansar.</h2>
        <a class="btn btn-home-primary" href="{{ route('rooms.index') }}">Ver disponibilidad</a>
    </div>
</section>
@endsection
