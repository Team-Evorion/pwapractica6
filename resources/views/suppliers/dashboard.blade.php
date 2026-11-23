@extends('layouts.app')

@section('title', 'Portal de Proveedores | Sistema de Reservas de Hotel')

@section('content')
<div class="bg-white border-start border-4 rounded-3 shadow-sm p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="border-color: #0F766E !important;">
    <div>
        <h1 class="h4 fw-bold mb-1">Portal de Proveedores</h1>
        <p class="text-muted small mb-0">Consulta las necesidades del hotel y presenta ofertas vinculadas o gestiona tu catalogo.</p>
    </div>
    <div class="d-flex gap-3">
        <div class="text-end">
            <div class="small text-muted">Ofertas / Suministros</div>
            <div class="fw-bold fs-5">{{ $totalItems }}</div>
        </div>
        <div class="text-end border-start ps-3">
            <div class="small text-muted">Unidades totales</div>
            <div class="fw-bold fs-5 text-hotel-teal">{{ $totalUnits }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h2 class="h6 fw-bold mb-0">Necesidades Abiertas del Hotel</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Necesidad del Hotel</th>
                    <th>Cantidad Requerida</th>
                    <th>Detalle</th>
                    <th>Ofertas Recibidas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hotelNeeds as $need)
                    <tr>
                        <td class="fw-semibold">#{{ $need->id }}</td>
                        <td class="fw-medium">{{ $need->name }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $need->quantity }}</span></td>
                        <td class="small text-muted">{{ $need->notes ?: 'Sin observaciones adicionales' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $need->offers_count }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            No hay necesidades abiertas del hotel en este momento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h6 fw-bold mb-0">Presentar Oferta / Registrar Suministro</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('suppliers.supplies.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="hotel_need_id" class="form-label fw-medium">Vincular a necesidad del hotel (opcional)</label>
                        <select
                            id="hotel_need_id"
                            name="hotel_need_id"
                            class="form-select @error('hotel_need_id') is-invalid @enderror"
                        >
                            <option value="">Suministro general / sin vincular</option>
                            @foreach ($hotelNeeds as $need)
                                <option value="{{ $need->id }}" @selected((string) old('hotel_need_id') === (string) $need->id)>
                                    #{{ $need->id }} - {{ $need->name }} (Req: {{ $need->quantity }})
                                </option>
                            @endforeach
                        </select>
                        @error('hotel_need_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Nombre de la oferta o suministro</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Ej. Oferta sabanas algodon 300 hilos"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="quantity" class="form-label fw-medium">Cantidad</label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                min="1"
                                value="{{ old('quantity', 1) }}"
                                class="form-control @error('quantity') is-invalid @enderror"
                                required
                            >
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label for="unit_price" class="form-label fw-medium">Precio unitario</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="unit_price"
                                name="unit_price"
                                value="{{ old('unit_price') }}"
                                class="form-control @error('unit_price') is-invalid @enderror"
                                placeholder="0.00"
                            >
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-medium">Notas de entrega</label>
                        <input
                            type="text"
                            id="notes"
                            name="notes"
                            value="{{ old('notes') }}"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Tiempo de entrega o condiciones"
                        >
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-hotel fw-semibold">
                            Enviar Oferta / Publicar
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
                            placeholder="Buscar en mis ofertas y suministros..."
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
                            <th>Oferta / Suministro</th>
                            <th>Necesidad Vinculada</th>
                            <th>Cantidad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supplies as $supply)
                            <tr>
                                <td class="fw-semibold">#{{ $supply->id }}</td>
                                <td>
                                    <div class="fw-medium">{{ $supply->name }}</div>
                                    @if (! is_null($supply->unit_price))
                                        <div class="small text-muted">${{ number_format((float) $supply->unit_price, 2) }} c/u</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($supply->hotelNeed)
                                        <span class="badge bg-info text-dark">#{{ $supply->hotelNeed->id }} - {{ $supply->hotelNeed->name }}</span>
                                    @else
                                        <span class="text-muted small">Sin necesidad vinculada</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $supply->quantity }}</span></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        @can('update', $supply)
                                            <a href="{{ route('suppliers.supplies.edit', $supply) }}" class="btn btn-sm btn-outline-primary">
                                                Editar
                                            </a>
                                        @endcan

                                        @can('delete', $supply)
                                            <form method="POST" action="{{ route('suppliers.supplies.destroy', $supply) }}" class="d-inline" onsubmit="return confirm('Eliminar este registro de tu catalogo?');">
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
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Aun no tienes ofertas ni suministros registrados.
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