<?php

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaidRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', Rule::exists('referral_salaries')],
            'paid' => ['required', 'bool'],
            'comment' => ['nullable', 'string']
        ];
    }
}
