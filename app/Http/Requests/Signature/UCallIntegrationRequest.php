<?php

namespace App\Http\Requests\Signature;

use Illuminate\Foundation\Http\FormRequest;

class UCallIntegrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app_id' => ['required', 'string'],
            'api_key' => ['required', 'string']
        ];
    }
}
