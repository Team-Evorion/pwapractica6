<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Reservas de Hotel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-hotel shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ url('/') }}">
                Hotel Evorion
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                    @auth
                        <li class="nav-item">
                            <span class="badge bg-light text-dark">
                                {{ auth()->user()->name }} ({{ auth()->user()->role?->display_name ?? 'Sin rol' }})
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('rooms.index') }}">Habitaciones</a>
                        </li>
                        @can('viewAny', \App\Models\Booking::class)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bookings.index') }}">Reservaciones</a>
                            </li>
                        @endcan
                        @if (auth()->user()->hasRole(\App\Models\Role::ADMINISTRATOR))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Panel Admin</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users.index') }}">Usuarios</a>
                            </li>
                        @endif
                        @if (auth()->user()->hasRole(\App\Models\Role::SUPPLIER))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('suppliers.dashboard') }}">Proveedores</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-hotel btn-sm" href="{{ route('register') }}">Registrarse</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
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

    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            Hotel Evorion &mdash; Habitaciones, reservaciones y servicios integrados
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
