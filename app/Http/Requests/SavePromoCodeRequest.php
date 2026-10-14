<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePromoCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
            'expired_at' => ['required', 'string'],
            'rate' => ['required', 'string', 'min:1', 'max:100']
        ];
    }
}
