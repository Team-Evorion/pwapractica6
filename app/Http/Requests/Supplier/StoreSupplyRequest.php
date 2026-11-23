<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'hotel_need_id' => [
                'nullable',
                'integer',
                Rule::exists('supplies', 'id')->where(function ($query) {
                    $query->where('item_type', 'need')
                        ->where('status', 'open');
                }),
            ],
            'unit_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del suministro u oferta es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'quantity.required' => 'La cantidad disponible es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un numero entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'hotel_need_id.exists' => 'Solo puedes presentar ofertas sobre necesidades abiertas del hotel.',
            'unit_price.numeric' => 'El precio unitario debe ser un numero valido.',
            'unit_price.min' => 'El precio unitario no puede ser negativo.',
        ];
    }
}
