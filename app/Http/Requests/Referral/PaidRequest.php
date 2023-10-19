<?php

namespace App\Http\Requests\Referral;

use App\Service\Referral\Core\PaidType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PaidRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(PaidType::class)],
            'date' => ['required', "date"],
            'amount' => ['required', 'integer'],
            'comment' => ['nullable', 'string']
        ];
    }
}
