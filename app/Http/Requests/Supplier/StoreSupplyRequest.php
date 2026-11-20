<?php

namespace App\Http\Requests\Supplier;

use App\Models\Supply;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Supply::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del suministro u oferta es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'quantity.required' => 'La cantidad disponible es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad no puede ser negativa.',
        ];
    }
}