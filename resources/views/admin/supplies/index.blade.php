@extends('layouts.app')

@section('title', 'Suministros | Administración')

@push('styles')
<style>{!! file_get_contents(resource_path('css/modules/supplies.css')) !!}</style>
@endpush

@section('content')
<div class="supplies-heading mb-4">
    <div>
        <div class="eyebrow">Administración</div>
        <h1 class="h3 fw-bold mt-2 mb-1">Necesidades y ofertas</h1>
        <p class="text-muted mb-0">Publica lo que necesita el hotel y revisa las propuestas enviadas por los proveedores.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i> Volver al panel</a>
</div>

<div class="row g-3 mb-4 supply-stats-grid">
    @foreach ([
        ['label' => 'Registros totales', 'value' => $summary['total'], 'icon' => 'collection'],
        ['label' => 'Necesidades abiertas', 'value' => $summary['open_needs'], 'icon' => 'clipboard-check'],
        ['label' => 'Ofertas recibidas', 'value' => $summary['offers'], 'icon' => 'send-check'],
        ['label' => 'Proveedores activos', 'value' => $summary['suppliers'], 'icon' => 'truck'],
    ] as $item)
        <div class="col-6 col-xl-3">
            <article class="supply-stat">
                <span class="dashboard-stat-icon"><i class="bi bi-{{ $item['icon'] }}"></i></span>
                <strong>{{ $item['value'] }}</strong>
                <span>{{ $item['label'] }}</span>
            </article>
        </div>
    @endforeach
</div>

<div class="row g-4 align-items-start supplies-layout">
    <div class="col-12 col-xl-4">
        <section class="card rounded-4 sticky-xl-top supply-create-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="dashboard-stat-icon"><i class="bi bi-plus-lg"></i></span>
                    <div>
                        <h2 class="h5 fw-bold mb-0">Nueva necesidad</h2>
                        <small class="text-muted">Será visible para todos los proveedores.</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.supplies.needs.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="need-name">Producto o servicio</label>
                        <input id="need-name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Ej. Toallas hoteleras" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="need-quantity">Cantidad requerida</label>
                        <input id="need-quantity" name="quantity" type="number" min="1" max="1000000" value="{{ old('quantity', 1) }}" class="form-control @error('quantity') is-invalid @enderror" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="need-notes">Detalle</label>
                        <textarea id="need-notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" placeholder="Características, fecha o condiciones de entrega">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button class="btn btn-hotel w-100" type="submit"><i class="bi bi-megaphone me-1"></i> Publicar necesidad</button>
                </form>
            </div>
        </section>
    </div>

    <div class="col-12 col-xl-8">
        <section class="card rounded-4 overflow-hidden">
            <div class="card-body border-bottom p-3 p-lg-4">
                <form method="GET" action="{{ route('admin.supplies.index') }}" class="row g-2">
                    <div class="col-12 col-lg-5">
                        <label class="visually-hidden" for="supply-search">Buscar</label>
                        <input id="supply-search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Buscar registro o proveedor...">
                    </div>
                    <div class="col-6 col-lg-2">
                        <select name="type" class="form-select" aria-label="Filtrar por tipo">
                            <option value="">Todos</option>
                            <option value="need" @selected(($filters['type'] ?? '') === 'need')>Necesidades</option>
                            <option value="offer" @selected(($filters['type'] ?? '') === 'offer')>Ofertas</option>
                            <option value="supply" @selected(($filters['type'] ?? '') === 'supply')>Suministros</option>
                        </select>
                    </div>
                    <div class="col-6 col-lg-2">
                        <select name="status" class="form-select" aria-label="Filtrar por estado">
                            <option value="">Estados</option>
                            <option value="open" @selected(($filters['status'] ?? '') === 'open')>Abierta</option>
                            <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>Cerrada</option>
                            <option value="offered" @selected(($filters['status'] ?? '') === 'offered')>Ofertada</option>
                        </select>
                    </div>
                    <div class="col-8 col-lg-2 d-grid"><button class="btn btn-dark" type="submit"><i class="bi bi-search"></i> Filtrar</button></div>
                    <div class="col-4 col-lg-1 d-grid"><a href="{{ route('admin.supplies.index') }}" class="btn btn-soft" aria-label="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table supply-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Registro</th>
                            <th>Responsable</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th class="text-end">Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supplies as $supply)
                            @php
                                $typeLabel = match ($supply->item_type) {
                                    'need' => 'Necesidad',
                                    'offer' => 'Oferta',
                                    default => 'Suministro',
                                };
                                $statusLabel = match ($supply->status) {
                                    'open' => 'Abierta',
                                    'closed' => 'Cerrada',
                                    'offered' => 'Ofertada',
                                    default => ucfirst($supply->status),
                                };
                            @endphp
                            <tr>
                                <td data-label="Registro">
                                    <span class="type-pill type-{{ $supply->item_type }}">{{ $typeLabel }}</span>
                                    <div class="fw-semibold mt-2">{{ $supply->name }}</div>
                                    <div class="small text-muted">{{ $supply->notes ?: 'Sin observaciones' }}</div>
                                    @if ($supply->hotelNeed)
                                        <div class="linked-need"><i class="bi bi-link-45deg"></i> {{ $supply->hotelNeed->name }}</div>
                                    @elseif ($supply->item_type === 'need')
                                        <div class="small text-hotel-teal mt-1">{{ $supply->offers_count }} oferta(s) recibida(s)</div>
                                    @endif
                                </td>
                                <td data-label="Responsable">
                                    @if ($supply->item_type === 'need')
                                        <span class="fw-medium">Hotel Evorion</span>
                                    @else
                                        <span class="fw-medium">{{ $supply->supplier?->name ?? 'Sin proveedor' }}</span>
                                        <div class="small text-muted">{{ $supply->supplier?->email }}</div>
                                    @endif
                                </td>
                                <td data-label="Cantidad"><strong>{{ $supply->quantity }}</strong></td>
                                <td data-label="Precio">{{ $supply->unit_price !== null ? '$'.number_format((float) $supply->unit_price, 2) : '—' }}</td>
                                <td data-label="Estado"><span class="status-pill status-{{ $supply->status }}">{{ $statusLabel }}</span></td>
                                <td data-label="Gestión" class="text-end">
                                    @if ($supply->item_type === 'need')
                                        <form method="POST" action="{{ route('admin.supplies.needs.status', $supply) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $supply->status === 'open' ? 'closed' : 'open' }}">
                                            <button class="btn btn-sm {{ $supply->status === 'open' ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit">
                                                {{ $supply->status === 'open' ? 'Cerrar' : 'Reabrir' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="small text-muted">Solo lectura</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay registros con esos filtros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($supplies->hasPages())
                <div class="card-footer bg-white p-3">{{ $supplies->links() }}</div>
            @endif
        </section>
    </div>
</div>
@endsection
