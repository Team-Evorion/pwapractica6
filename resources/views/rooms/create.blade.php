@extends('layouts.rooms')

@section('title', 'Nueva habitación')

@section('content')
    <section class="container form-section">
        <a class="back-link" href="{{ route('rooms.manage') }}">← Volver a administración</a>
        <div class="form-card">
            <div class="form-heading">
                <p class="eyebrow">Gestión del hotel</p>
                <h1>Nueva habitación</h1>
            </div>
            <form method="POST" action="{{ route('rooms.store') }}">
                @csrf
                @include('rooms._form')
                <div class="form-actions">
                    <a class="btn btn-outline-secondary" href="{{ route('rooms.manage') }}">Cancelar</a>
                    <button class="btn btn-hotel" type="submit">Guardar habitación</button>
                </div>
            </form>
        </div>
    </section>
@endsection
