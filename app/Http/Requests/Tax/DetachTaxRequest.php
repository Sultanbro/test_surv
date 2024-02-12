<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\UserTaxDTO;
use Illuminate\Foundation\Http\FormRequest;

class DetachTaxRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'tax_id' => 'required|integer|exists:taxes,id'
        ];
    }

    /**
     * @return UserTaxDTO
     */
    public function toDto(): UserTaxDTO
    {
        return new UserTaxDTO(
            taxId: $this->get('tax_id'),
            userId: $this->get('user_id'),
            value: null,
            isPercent: null,
            endSubtraction: null
        );
    }
}
