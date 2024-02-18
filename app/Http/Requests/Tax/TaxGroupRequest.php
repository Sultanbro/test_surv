<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\CreateTaxDTO;
use App\DTO\Tax\TaxGroupDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

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
            'name' => [
                'required',
                'string',
                Rule::unique('tax_groups')->ignore($this->route('id'))
            ],
            // tax group items
            'items' => 'required|array|min:1',
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
            name: $this->get('name'),
            items: $this->get('items')
        );
    }
}
