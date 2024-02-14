<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\CreateTaxDTO;
use App\DTO\Tax\TaxGroupDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class TaxGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            // tax group items
            'items.*.name' => 'required|string',
            'items.*.is_percent' => 'required|bool',
            'items.*.end_subtraction' => 'required|bool',
            'items.*.value' => 'required|integer',
            'items.*.order' => 'required|integer',
        ];
    }

    /**
     * @return TaxGroupDTO
     */
    public function toDto(): TaxGroupDTO
    {
        return new TaxGroupDTO(
            id: null,
            name: $this->get('name'),
            items: $this->get('items')
        );
    }
}
