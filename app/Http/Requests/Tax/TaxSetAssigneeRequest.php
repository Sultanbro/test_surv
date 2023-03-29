<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\SetAssignedTaxDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class TaxSetAssigneeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id'       => 'required|integer|exists:users,id',
            'tax_id'        => 'required|integer|exists:taxes,id',
            'is_assigned'   => 'required|bool'
        ];
    }

    /**
     * @return SetAssignedTaxDTO
     */
    public function toDto(): SetAssignedTaxDTO
    {
        $validated = $this->validated();

        $userId     = Arr::get($validated, 'user_id');
        $taxId      = Arr::get($validated, 'tax_id');
        $isAssigned = Arr::get($validated, 'is_assigned');

        return new SetAssignedTaxDTO(
            $taxId,
            $userId,
            $isAssigned
        );
    }
}
