<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Models\Supply;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelNeedStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(Role::ADMINISTRATOR) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([Supply::STATUS_OPEN, Supply::STATUS_CLOSED])],
        ];
    }
}
