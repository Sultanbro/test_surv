<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\SetAssignedTaxDTO;
use App\DTO\Tax\SetUserTaxDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SetTaxGroupAssigneeRequest extends FormRequest
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
            'tax_group_id' => 'required|integer|exists:tax_groups,id',
            'assigned' => 'required|bool'
        ];
    }

    /**
     * @return SetUserTaxDTO
     */
    public function toDto(): SetUserTaxDTO
    {
        return new SetUserTaxDTO(
            taxGroupId: $this->get('tax_group_id'),
            userId: $this->get('user_id'),
            assigned: $this->get('assigned')
        );
    }
}
