@php($currentRoom = $room ?? null)

<div class="row g-4">
    <div class="col-12 col-md-6">
        <label class="form-label" for="room_number">Número de habitación</label>
        <input class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" type="number" min="1" value="{{ old('room_number', $currentRoom?->room_number) }}" required>
        @error('room_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="room_type">Tipo</label>
        <select class="form-select @error('room_type') is-invalid @enderror" id="room_type" name="room_type" required>
            <option value="">Selecciona un tipo</option>
            @foreach ($roomTypes as $type)
                <option value="{{ $type }}" @selected(old('room_type', $currentRoom?->room_type) === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('room_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="room_price">Precio por noche</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input class="form-control @error('room_price') is-invalid @enderror" id="room_price" name="room_price" type="number" min="0" step="0.01" value="{{ old('room_price', $currentRoom?->room_price) }}" required>
            @error('room_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="capacity">Capacidad</label>
        <input class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" type="number" min="1" max="20" value="{{ old('capacity', $currentRoom?->capacity ?? 1) }}" required>
        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Descripción</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" maxlength="2000">{{ old('description', $currentRoom?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <input name="is_available" type="hidden" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" id="is_available" name="is_available" type="checkbox" value="1" @checked((bool) old('is_available', $currentRoom?->is_available ?? true))>
            <label class="form-check-label" for="is_available">Habitación habilitada para reservas</label>
        </div>
        @error('is_available')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
    </div>
</div>
