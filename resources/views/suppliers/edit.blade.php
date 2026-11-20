@extends('layouts.app')

@section('title', 'Editar Suministro | Portal de Proveedores')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Editar Suministro / Oferta</h1>
            <a href="{{ route('suppliers.dashboard') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('suppliers.supplies.update', $supply) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Nombre del suministro u oferta</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $supply->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
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
                            value="{{ old('quantity', $supply->quantity) }}"
                            class="form-control @error('quantity') is-invalid @enderror"
                            required
                        >
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('suppliers.dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-hotel fw-semibold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection