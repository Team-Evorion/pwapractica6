<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Room::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'room_number' => ['required', 'integer', 'min:1', 'max:4294967295', 'unique:rooms,room_number'],
            'room_type' => ['required', 'string', Rule::in(Room::TYPES)],
            'room_price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'is_available' => ['required', 'boolean'],
            'description' => ['nullable', 'string', 'max:2000'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_number.unique' => 'Ya existe una habitación con este número.',
            'room_type.in' => 'Selecciona un tipo de habitación válido.',
            'room_price.decimal' => 'El precio puede tener un máximo de dos decimales.',
            'is_available.boolean' => 'El estado de disponibilidad no es válido.',
            'image.image' => 'Selecciona una imagen válida.',
            'image.mimes' => 'La imagen debe usar formato JPG, PNG o WebP.',
            'image.max' => 'La imagen no puede superar 4 MB.',
        ];
    }
}
