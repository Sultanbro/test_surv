<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:190'],
            'last_name' => ['string', 'max:190', 'nullable'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'min:11', 'max:30', 'unique:users'],
            'g-recaptcha-response' => [Rule::requiredIf(!app()->environment('testing')), 'recaptcha'],
            'currency' => [
                'required', Rule::in([
                    'kzt',
                    'rub',
                    'usd',
                ]),
            ],
        ];
    }
}
