@extends('layouts.rooms')

@section('title', 'Habitación '.$room->room_number)

@section('content')
    <section class="container detail-section">
        <a class="back-link" href="{{ route('rooms.index') }}">← Volver al catálogo</a>
        <div class="row g-4 g-lg-5 align-items-stretch">
            <div class="col-12 col-lg-7">
                @if ($room->image_path)
                    <img class="detail-image" src="{{ asset($room->image_path) }}" alt="Habitación {{ $room->room_number }}">
                @else
                    <div class="detail-image room-placeholder room-placeholder-{{ strtolower($room->room_type) }}">
                        <span>{{ $room->room_type }}</span>
                    </div>
                @endif
            </div>
            <div class="col-12 col-lg-5">
                <div class="detail-panel">
                    <p class="eyebrow">Habitación {{ $room->room_number }}</p>
                    <h1>{{ $room->room_type }}</h1>
                    <p class="detail-description">{{ $room->description ?: 'Una habitación preparada para una estancia cómoda y tranquila.' }}</p>
                    <dl class="room-facts">
                        <div>
                            <dt>Capacidad</dt>
                            <dd>{{ $room->capacity }} {{ $room->capacity === 1 ? 'huésped' : 'huéspedes' }}</dd>
                        </div>
                        <div>
                            <dt>Estado</dt>
                            <dd>{{ $room->is_available ? 'Disponible' : 'Fuera de servicio' }}</dd>
                        </div>
                    </dl>
                    <p class="detail-price"><strong>${{ number_format((float) $room->room_price, 2) }}</strong><span> por noche</span></p>

                    @auth
                        @if (auth()->user()->hasRole(\App\Models\Role::CUSTOMER))
                        <hr class="my-4">
                        <h2 class="h4">Reservar esta habitación</h2>
                        <p class="text-muted">Selecciona las fechas de tu estadía. La reserva quedará pendiente de confirmación.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                Revisa las fechas indicadas antes de continuar.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('bookings.store') }}" data-booking-form>
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">

                            @error('room_id')
                                <div class="alert alert-danger py-2" role="alert">{{ $message }}</div>
                            @enderror

                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="check_in_date">Entrada</label>
                                    <input
                                        class="form-control @error('check_in_date') is-invalid @enderror"
                                        id="check_in_date"
                                        name="check_in_date"
                                        type="date"
                                        min="{{ now()->toDateString() }}"
                                        value="{{ old('check_in_date') }}"
                                        required
                                    >
                                    @error('check_in_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="check_out_date">Salida</label>
                                    <input
                                        class="form-control @error('check_out_date') is-invalid @enderror"
                                        id="check_out_date"
                                        name="check_out_date"
                                        type="date"
                                        min="{{ now()->addDay()->toDateString() }}"
                                        value="{{ old('check_out_date') }}"
                                        required
                                    >
                                    @error('check_out_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button class="btn btn-hotel w-100 mt-3" type="submit">Solicitar reserva</button>
                        </form>
                        @elseif (auth()->user()->hasRole(\App\Models\Role::RECEPTIONIST, \App\Models\Role::ADMINISTRATOR))
                            <div class="alert alert-light border mt-4 mb-0">
                                <p class="mb-3">Registra la reservación a nombre del cliente desde el panel de Recepción.</p>
                                <a class="btn btn-hotel w-100" href="{{ route('bookings.index', ['check_in_date' => old('check_in_date'), 'check_out_date' => old('check_out_date')]) }}">Abrir reservaciones</a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-light border mt-4 mb-0">
                            <p class="mb-3">Inicia sesión para elegir las fechas y reservar esta habitación.</p>
                            <a class="btn btn-hotel w-100" href="{{ route('login') }}">Iniciar sesión</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.querySelector('[data-booking-form]');

            if (!form) {
                return;
            }

            const checkIn = form.querySelector('#check_in_date');
            const checkOut = form.querySelector('#check_out_date');

            const updateCheckOutMinimum = () => {
                if (!checkIn.value) {
                    return;
                }

                const minimum = new Date(`${checkIn.value}T00:00:00`);
                minimum.setDate(minimum.getDate() + 1);
                const minimumValue = minimum.toISOString().slice(0, 10);

                checkOut.min = minimumValue;

                if (checkOut.value && checkOut.value < minimumValue) {
                    checkOut.value = '';
                }
            };

            checkIn.addEventListener('change', updateCheckOutMinimum);
            updateCheckOutMinimum();
        })();
    </script>
@endpush
