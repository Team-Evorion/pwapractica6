@extends('layouts.app')

@section('title', 'Portal de Proveedores | Sistema de Reservas de Hotel')

@section('content')
<div class="bg-white border-start border-4 rounded-3 shadow-sm p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="border-color: #0F766E !important;">
    <div>
        <h1 class="h4 fw-bold mb-1">Portal de Proveedores</h1>
        <p class="text-muted small mb-0">Gestiona tus suministros y ofertas disponibles para el hotel.</p>
    </div>
    <div class="d-flex gap-3">
        <div class="text-end">
            <div class="small text-muted">Ítems propios</div>
            <div class="fw-bold fs-5">{{ $totalItems }}</div>
        </div>
        <div class="text-end border-start ps-3">
            <div class="small text-muted">Unidades totales</div>
            <div class="fw-bold fs-5 text-hotel-teal">{{ $totalUnits }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h6 fw-bold mb-0">Registrar Oferta / Suministro</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('suppliers.supplies.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Nombre del suministro u oferta</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Ej. Juego de sábanas premium"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="form-label fw-medium">Cantidad disponible</label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="0"
                            value="{{ old('quantity', 1) }}"
                            class="form-control @error('quantity') is-invalid @enderror"
                            required
                        >
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-hotel fw-semibold">
                            Publicar Suministro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <form method="GET" action="{{ route('suppliers.dashboard') }}" class="row g-2 align-items-center">
                    <div class="col">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control form-control-sm"
                            placeholder="Buscar en mis suministros..."
                        >
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark btn-sm">Buscar</button>
                        @if ($search !== '')
                            <a href="{{ route('suppliers.dashboard') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Suministro / Oferta</th>
                            <th>Cantidad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supplies as $supply)
                            <tr>
                                <td class="fw-semibold">#{{ $supply->id }}</td>
                                <td class="fw-medium">{{ $supply->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $supply->quantity }}</span></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        @can('update', $supply)
                                            <a href="{{ route('suppliers.supplies.edit', $supply) }}" class="btn btn-sm btn-outline-primary">
                                                Editar
                                            </a>
                                        @endcan

                                        @can('delete', $supply)
                                            <form method="POST" action="{{ route('suppliers.supplies.destroy', $supply) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este suministro de tu catálogo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    Aún no tienes suministros u ofertas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($supplies->hasPages())
                <div class="card-footer bg-white">
                    {{ $supplies->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection