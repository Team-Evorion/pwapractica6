<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(
            Role::CUSTOMER,
            Role::RECEPTIONIST,
            Role::ADMINISTRATOR,
        ) ?? false;
    }

    public function rules(): array
    {
        $isCustomer = $this->user()?->hasRole(Role::CUSTOMER) ?? false;

        return [
            'user_id' => $isCustomer
                ? ['exclude']
                : ['required', 'integer', 'exists:users,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->user()?->hasRole(Role::CUSTOMER) || ! $this->filled('user_id')) {
                    return;
                }

                $customer = User::with('role')->find($this->integer('user_id'));
                if (! $customer?->is_active || ! $customer->hasRole(Role::CUSTOMER)) {
                    $validator->errors()->add('user_id', 'Debes seleccionar un cliente activo.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Selecciona el cliente para la reservación.',
            'room_id.required' => 'Selecciona una habitación.',
            'room_id.exists' => 'La habitación seleccionada no existe.',
            'check_in_date.after_or_equal' => 'La fecha de entrada no puede estar en el pasado.',
            'check_out_date.after' => 'La fecha de salida debe ser posterior a la entrada.',
        ];
    }
}
