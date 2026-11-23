@extends('layouts.app')

@section('title', 'Finanzas | Hotel Evorion')

@push('styles')
<style>{!! file_get_contents(resource_path('css/modules/finances.css')) !!}</style>
@endpush

@section('content')
<section class="finance-hero mb-4">
    <div>
        <span class="finance-eyebrow">Control gerencial</span>
        <h1>Finanzas del hotel</h1>
        <p>Ingresos, cobros, saldos y ocupación calculados desde las reservaciones.</p>
    </div>
    <a class="btn btn-soft" href="{{ route('dashboard') }}"><i class="bi bi-arrow-left me-1"></i> Volver al panel</a>
</section>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>No se pudo registrar el movimiento.</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<section class="row g-3 mb-4" aria-label="Resumen financiero">
    @foreach ([
        ['label' => 'Ventas confirmadas', 'value' => '$'.number_format($summary['confirmed_revenue'], 2), 'detail' => 'Valor contratado', 'icon' => 'receipt'],
        ['label' => 'Cobrado neto', 'value' => '$'.number_format($summary['net_collected'], 2), 'detail' => 'Cobros menos reembolsos', 'icon' => 'cash-stack'],
        ['label' => 'Saldo por cobrar', 'value' => '$'.number_format($summary['pending_balance'], 2), 'detail' => 'Solo reservas confirmadas', 'icon' => 'hourglass-split'],
        ['label' => 'Ocupación del mes', 'value' => number_format($summary['occupancy'], 1).'%', 'detail' => 'Noches confirmadas', 'icon' => 'bar-chart'],
    ] as $item)
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="finance-stat">
                <span class="finance-stat-icon"><i class="bi bi-{{ $item['icon'] }}"></i></span>
                <small>{{ $item['label'] }}</small>
                <strong>{{ $item['value'] }}</strong>
                <span>{{ $item['detail'] }}</span>
            </article>
        </div>
    @endforeach
</section>

<div class="row g-4 mb-4">
    <div class="col-12 col-xl-8">
        <section class="finance-panel h-100">
            <div class="finance-panel-heading">
                <div>
                    <span class="finance-eyebrow">Últimos seis meses</span>
                    <h2>Ventas y dinero cobrado</h2>
                </div>
                <div class="chart-legend"><span class="legend-sales">Ventas</span><span class="legend-paid">Cobrado</span></div>
            </div>
            <div class="finance-chart" role="img" aria-label="Comparación mensual de ventas confirmadas y cobros">
                @foreach ($monthlyPerformance as $month)
                    <div class="chart-row">
                        <span class="chart-label">{{ $month['label'] }}</span>
                        <div class="chart-bars">
                            <div class="chart-track"><span class="chart-bar chart-sales" style="--bar-size: {{ round(($month['sales'] / $chartMaximum) * 100, 1) }}%"></span></div>
                            <div class="chart-track"><span class="chart-bar chart-paid" style="--bar-size: {{ round(($month['collected'] / $chartMaximum) * 100, 1) }}%"></span></div>
                        </div>
                        <span class="chart-value">${{ number_format($month['collected'], 0) }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
    <div class="col-12 col-xl-4">
        <aside class="finance-panel finance-overview h-100">
            <span class="finance-eyebrow">Panorama</span>
            <h2>Proyección operativa</h2>
            <div class="overview-value">${{ number_format($summary['projected_revenue'], 2) }}</div>
            <p>Reservas pendientes y confirmadas previstas.</p>
            <div class="overview-line"><span>Reembolsado</span><strong>${{ number_format($summary['refunds'], 2) }}</strong></div>
            <div class="overview-line"><span>Disponible cobrado</span><strong>${{ number_format($summary['net_collected'], 2) }}</strong></div>
            <small>Los valores se actualizan al registrar movimientos.</small>
        </aside>
    </div>
</div>

<section class="finance-panel mb-4">
    <div class="finance-panel-heading align-items-end">
        <div>
            <span class="finance-eyebrow">Detalle</span>
            <h2>Estado de las reservaciones</h2>
        </div>
    </div>

    <form method="GET" action="{{ route('finances.index') }}" class="row g-2 finance-filters">
        <div class="col-12 col-lg-4"><label class="visually-hidden" for="finance-search">Buscar</label><input id="finance-search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Cliente, correo o habitación..."></div>
        <div class="col-6 col-lg-2"><label class="visually-hidden" for="finance-status">Estado</label><select id="finance-status" name="status" class="form-select"><option value="">Todos los estados</option><option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pendiente</option><option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>Confirmada</option><option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Cancelada</option></select></div>
        <div class="col-6 col-lg-2"><label class="visually-hidden" for="finance-from">Desde</label><input id="finance-from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="form-control" title="Entrada desde"></div>
        <div class="col-6 col-lg-2"><label class="visually-hidden" for="finance-to">Hasta</label><input id="finance-to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="form-control" title="Salida hasta"></div>
        <div class="col-4 col-lg-1 d-grid"><button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button></div>
        <div class="col-2 col-lg-1 d-grid"><a class="btn btn-soft" href="{{ route('finances.index') }}" aria-label="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
    </form>

    <div class="finance-bookings">
        @forelse ($bookings as $booking)
            @php
                $netPaid = $booking->netPaid();
                $balance = $booking->balanceDue();
                $paymentState = $netPaid <= 0 ? ['Pendiente', 'pending'] : ($balance > 0.009 ? ['Pago parcial', 'partial'] : ['Pagada', 'paid']);
                $bookingState = match ($booking->status) {
                    'confirmed' => ['Confirmada', 'confirmed'],
                    'cancelled' => ['Cancelada', 'cancelled'],
                    default => ['Pendiente', 'pending'],
                };
            @endphp
            <article class="finance-booking-card">
                <div class="booking-main">
                    <div class="booking-identity">
                        <span class="booking-room">Habitación {{ $booking->room->room_number }}</span>
                        <h3>{{ $booking->user->name }}</h3>
                        <small>{{ $booking->user->email }} · {{ $booking->room->room_type }}</small>
                    </div>
                    <div class="booking-dates"><span>{{ $booking->check_in_date->format('d/m/Y') }} — {{ $booking->check_out_date->format('d/m/Y') }}</span><small>{{ $booking->nights() }} noche(s) · ${{ number_format((float) $booking->nightly_rate, 2) }} por noche</small></div>
                    <div class="booking-total"><small>Total</small><strong>${{ number_format($booking->totalAmount(), 2) }}</strong></div>
                    <div class="booking-badges"><span class="booking-pill booking-{{ $bookingState[1] }}">{{ $bookingState[0] }}</span><span class="payment-pill payment-{{ $paymentState[1] }}">{{ $paymentState[0] }}</span></div>
                </div>

                <div class="payment-progress" aria-label="Progreso de pago">
                    <div><span>Cobrado</span><strong>${{ number_format($netPaid, 2) }}</strong></div>
                    <div><span>Saldo</span><strong>${{ number_format($balance, 2) }}</strong></div>
                    <div class="progress flex-grow-1" role="progressbar" aria-valuenow="{{ $booking->totalAmount() > 0 ? round(($netPaid / $booking->totalAmount()) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $booking->totalAmount() > 0 ? min(100, round(($netPaid / $booking->totalAmount()) * 100)) : 0 }}%"></div></div>
                </div>

                <details class="payment-details">
                    <summary><i class="bi bi-wallet2"></i> Registrar movimiento y ver historial</summary>
                    <div class="payment-details-grid">
                        <form method="POST" action="{{ route('finances.payments.store', $booking) }}" class="payment-form">
                            @csrf
                            <h4>Nuevo movimiento</h4>
                            <div class="row g-2">
                                <div class="col-6"><label class="form-label">Tipo</label><select name="kind" class="form-select" required><option value="payment">Cobro</option><option value="refund">Reembolso</option></select></div>
                                <div class="col-6"><label class="form-label">Valor</label><input name="amount" type="number" min="0.01" max="999999.99" step="0.01" class="form-control" value="{{ $balance > 0 ? number_format($balance, 2, '.', '') : '' }}" required></div>
                                <div class="col-6"><label class="form-label">Método</label><select name="method" class="form-select" required><option value="cash">Efectivo</option><option value="card">Tarjeta</option><option value="transfer">Transferencia</option></select></div>
                                <div class="col-6"><label class="form-label">Fecha</label><input name="paid_at" type="date" max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" class="form-control" required></div>
                                <div class="col-12"><label class="form-label">Referencia</label><input name="reference" maxlength="100" class="form-control" placeholder="Opcional: comprobante o transacción"></div>
                                <div class="col-12"><label class="form-label">Nota</label><textarea name="notes" maxlength="500" rows="2" class="form-control" placeholder="Detalle opcional"></textarea></div>
                            </div>
                            <button class="btn btn-hotel w-100 mt-3" type="submit">Guardar movimiento</button>
                        </form>
                        <div class="payment-history">
                            <h4>Historial</h4>
                            @forelse ($booking->payments as $payment)
                                <div class="history-item">
                                    <span class="history-icon {{ $payment->kind === 'refund' ? 'is-refund' : '' }}"><i class="bi bi-{{ $payment->kind === 'refund' ? 'arrow-counterclockwise' : 'check-lg' }}"></i></span>
                                    <div><strong>{{ $payment->kind === 'refund' ? 'Reembolso' : 'Cobro' }} · ${{ number_format((float) $payment->amount, 2) }}</strong><small>{{ $payment->paid_at->format('d/m/Y') }} · {{ match ($payment->method) { 'card' => 'Tarjeta', 'transfer' => 'Transferencia', default => 'Efectivo' } }} · {{ $payment->recordedBy->name }}</small>@if ($payment->reference)<small>Ref. {{ $payment->reference }}</small>@endif</div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">Todavía no existen movimientos para esta reservación.</p>
                            @endforelse
                        </div>
                    </div>
                </details>
            </article>
        @empty
            <div class="finance-empty"><i class="bi bi-receipt"></i><h3>No hay reservaciones</h3><p>Prueba con otros filtros o registra una reservación.</p></div>
        @endforelse
    </div>

    @if ($bookings->hasPages())<div class="finance-pagination">{{ $bookings->links() }}</div>@endif
</section>
@endsection
