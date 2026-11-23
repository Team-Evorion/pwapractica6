<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Reservas de Hotel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --hotel-teal: #0F766E;
            --hotel-navy: #0F172A;
            --hotel-gray: #F1F5F9;
            --hotel-white: #FFFFFF;
        }
        body {
            background-color: var(--hotel-gray);
            color: var(--hotel-navy);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-hotel {
            background-color: var(--hotel-navy);
        }
        .btn-hotel {
            background-color: var(--hotel-teal);
            border-color: var(--hotel-teal);
            color: var(--hotel-white);
        }
        .btn-hotel:hover {
            background-color: #115E59;
            border-color: #115E59;
            color: var(--hotel-white);
        }
        .text-hotel-teal {
            color: var(--hotel-teal);
        }
    </style>
    <link href="{{ asset('css/evorion-ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @php($me = auth()->user())
    <nav class="navbar navbar-expand-lg app-nav sticky-top" aria-label="Navegación principal">
        <div class="container-xl">
            <a class="navbar-brand app-brand" href="{{ route('home') }}">
                <span class="brand-mark">E</span> Hotel&nbsp;<span class="brand-accent">Evorion</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-lg-5 me-auto gap-lg-2">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">Habitaciones</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Mi panel</a></li>
                        @can('viewAny', \App\Models\Booking::class)
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">Reservaciones</a></li>
                        @endcan
                        @if (auth()->user()->hasRole(\App\Models\Role::ADMINISTRATOR))
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Administración</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.supplies.*') ? 'active' : '' }}" href="{{ route('admin.supplies.index') }}">Suministros</a></li>
                        @endif
                        @if (auth()->user()->hasRole(\App\Models\Role::SUPPLIER))
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.dashboard') }}">Proveedores</a></li>
                        @endif
                    @endauth
                </ul>
                <div class="d-flex align-items-lg-center flex-wrap gap-2 mt-3 mt-lg-0">
                    @auth
                        <span class="account-chip"><i class="bi bi-person-circle"></i> {{ $me->name }} <small>· {{ $me->role?->display_name ?? 'Sin rol' }}</small></span>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-soft btn-sm">Salir</button></form>
                    @else
                        <a class="btn btn-soft btn-sm" href="{{ route('login') }}">Iniciar sesión</a>
                        <a class="btn btn-hotel btn-sm" href="{{ route('register') }}">Crear cuenta</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container-xl page-wrap flex-grow-1">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if ($errors->has('general'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first('general') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer mt-auto">
        <div class="container-xl d-flex flex-column flex-md-row justify-content-between gap-2">
            <span><span class="footer-mark">E</span> Hotel Evorion</span>
            <small>Sistema de reservas · Proyecto universitario</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
