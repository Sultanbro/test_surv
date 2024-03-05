<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\EditUserTaxDTO;
use Illuminate\Foundation\Http\FormRequest;

class EditUserTaxGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'year' => 'required|integer',
            'month' => 'required|integer',
            'user_id' => 'required|integer',
            'reason' => 'required|string',
            'tax_group_id' => 'required|integer'
        ];
    }

    /**
     * @return EditUserTaxDTO
     */
    public function toDto(): EditUserTaxDTO
    {
        return new EditUserTaxDTO(
            year: $this->get('year'),
            month: $this->get('month'),
            userId: $this->get('user_id'),
            reason: $this->get('reason'),
            taxGroupId: $this->get('tax_group_id')
        );
    }
}
