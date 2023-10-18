<?php

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaidRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer'],
            'comment' => ['nullable', 'string']
        ];
    }
}
