<?php

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'absolute_earned' => ['nullable', 'string', Rule::in(['desc', 'asc'])],
            'date' => ['nullable', 'date']
        ];
    }
}
