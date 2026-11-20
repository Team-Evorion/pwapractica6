@extends('layouts.rooms')

@section('title', 'Editar habitación '.$room->room_number)

@section('content')
    <section class="container form-section">
        <a class="back-link" href="{{ route('rooms.manage') }}">← Volver a administración</a>
        <div class="form-card">
            <div class="form-heading">
                <p class="eyebrow">Habitación {{ $room->room_number }}</p>
                <h1>Editar habitación</h1>
            </div>
            <form method="POST" action="{{ route('rooms.update', $room) }}">
                @csrf
                @method('PUT')
                @include('rooms._form', ['room' => $room])
                <div class="form-actions">
                    <a class="btn btn-outline-secondary" href="{{ route('rooms.manage') }}">Cancelar</a>
                    <button class="btn btn-hotel" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </section>
@endsection
