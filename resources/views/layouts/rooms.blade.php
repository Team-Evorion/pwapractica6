<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Habitaciones') · Hotel Evorion</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/rooms.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg hotel-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-mark">E</span>
                <span>Hotel Evorion</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#roomsNavigation" aria-controls="roomsNavigation" aria-expanded="false" aria-label="Abrir navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="roomsNavigation">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    <a class="nav-link" href="{{ route('rooms.index') }}">Habitaciones</a>
                    @auth
                        <a class="nav-link" href="{{ route('dashboard') }}">Mi panel</a>
                        @can('viewAny', \App\Models\Booking::class)
                            <a class="nav-link" href="{{ route('bookings.index') }}">Reservaciones</a>
                        @endcan
                        @if (auth()->user()->hasRole(\App\Models\Role::ADMINISTRATOR))
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Administración</a>
                        @endif
                        @if (auth()->user()->hasRole(\App\Models\Role::SUPPLIER))
                            <a class="nav-link" href="{{ route('suppliers.dashboard') }}">Proveedores</a>
                        @endif
                        <span class="navbar-text text-white-50 px-lg-2">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-light" type="submit">Cerrar sesión</button>
                        </form>
                    @else
                        <a class="btn btn-sm btn-light ms-lg-2" href="{{ route('login') }}">Iniciar sesión</a>
                    @endauth
                    @can('viewAny', \App\Models\Room::class)
                        <a class="btn btn-sm btn-light ms-lg-2" href="{{ route('rooms.manage') }}">Administrar</a>
                    @endcan
                </div>
            </div>
        </div>
    </nav>

    @if (session('status'))
        <div class="container pt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="container pt-3">
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="hotel-footer">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>Hotel Evorion</span>
            <span>Catálogo de habitaciones</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/rooms.js') }}"></script>
    @stack('scripts')
</body>
</html>
