<?php

namespace App\Http\Requests\Signature;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewVerificationCodeRequest extends FormRequest
{
    public function rules(): array
    {
        /**@var User $user */
        $user = $this->user();
        return [
            'phone' => ['required', 'string'],
            'name' => ['required', 'string'],
            'contract_number' => ['nullable', 'string'],
            'password_number' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'images' => [Rule::requiredIf($user->has('signatureHistories')->count()), 'array', 'max:2'],
            'images.*' => ['sometimes', 'file', 'mimes:png,jpeg,jpg']
        ];
    }
}
