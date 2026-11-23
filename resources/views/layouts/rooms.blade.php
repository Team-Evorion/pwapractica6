<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Habitaciones') · Hotel Evorion</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/rooms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/evorion-ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @php($me = auth()->user())
    <nav class="navbar navbar-expand-lg hotel-navbar app-nav sticky-top" aria-label="Navegación principal">
        <div class="container-xl">
            <a class="navbar-brand app-brand" href="{{ route('home') }}">
                <span class="brand-mark">E</span> Hotel&nbsp;<span class="brand-accent">Evorion</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#roomsNavigation" aria-controls="roomsNavigation" aria-expanded="false" aria-label="Abrir navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="roomsNavigation">
                <div class="navbar-nav ms-lg-5 me-auto gap-lg-2">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                    <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">Habitaciones</a>
                    @auth
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Mi panel</a>
                        @can('viewAny', \App\Models\Booking::class)
                            <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">Reservaciones</a>
                        @endcan
                        @if (auth()->user()->hasRole(\App\Models\Role::ADMINISTRATOR))
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Administración</a>
                        @endif
                        @if (auth()->user()->hasRole(\App\Models\Role::SUPPLIER))
                            <a class="nav-link" href="{{ route('suppliers.dashboard') }}">Proveedores</a>
                        @endif
                    @endauth
                </div>
                <div class="d-flex align-items-lg-center flex-wrap gap-2 mt-3 mt-lg-0">
                    @auth
                        <span class="account-chip"><i class="bi bi-person-circle"></i> {{ $me->name }} <small>· {{ $me->role?->display_name ?? 'Sin rol' }}</small></span>
                        @can('viewAny', \App\Models\Room::class)
                            <a class="btn btn-hotel btn-sm" href="{{ route('rooms.manage') }}">Gestionar</a>
                        @endcan
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-soft btn-sm" type="submit">Salir</button></form>
                    @else
                        <a class="btn btn-soft btn-sm" href="{{ route('login') }}">Iniciar sesión</a>
                        <a class="btn btn-hotel btn-sm" href="{{ route('register') }}">Crear cuenta</a>
                    @endauth
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

    <footer class="hotel-footer app-footer">
        <div class="container-xl d-flex flex-column flex-md-row justify-content-between gap-2">
            <span><span class="footer-mark">E</span> Hotel Evorion</span>
            <small>Habitaciones, reservaciones y hospitalidad</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/rooms.js') }}"></script>
    @stack('scripts')
</body>
</html>
