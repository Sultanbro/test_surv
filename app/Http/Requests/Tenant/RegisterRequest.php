<?php

namespace App\Http\Requests\Tenant;

use App\Rules\CheckCentralUsers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'string', 'email', 'max:255', new CheckCentralUsers('email')],
            'phone' => ['required', 'string', 'min:11', 'max:30', new CheckCentralUsers('phone')],
            'g-recaptcha-response' => [
                Rule::requiredIf(!app()->environment('local')),
                Rule::excludeIf(app()->environment('local')),
                'recaptcha',
            ],
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
