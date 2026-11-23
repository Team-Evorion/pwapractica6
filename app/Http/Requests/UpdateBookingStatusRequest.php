<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Booking|null $booking */
        $booking = $this->route('booking');

        return $booking !== null && ($this->user()?->can('update', $booking) ?? false);
    }

    public function rules(): array
    {
        $allowed = $this->user()?->hasRole(Role::CUSTOMER)
            ? [Booking::STATUS_CANCELLED]
            : [Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED];

        return [
            'status' => ['required', 'string', Rule::in($allowed)],
        ];
    }
}
