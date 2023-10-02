<?php

namespace App\Http\Requests\Birthday;

use Illuminate\Foundation\Http\FormRequest;

class BirthdaySendGiftRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['numeric', 'required', 'min:0.01']
        ];
    }
}
