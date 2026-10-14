<?php

namespace App\Http\Requests\Signature;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', Rule::exists('sms_codes', 'code')]
        ];
    }
}
