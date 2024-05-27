<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class NewInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payer_name' => ['required', 'string'],
            'payer_phone' => ['required', 'string']
        ];
    }
}
