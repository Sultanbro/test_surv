<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\CreateTaxDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateTaxRequest extends FormRequest
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
            'name' => 'required|string',
            'value' => 'required|regex:/^\d*(\.\d{2})?$/',
            'is_percent' => 'bool',
            'end_subtraction' => 'bool'
        ];
    }

    /**
     * @return CreateTaxDTO
     */
    public function toDto(): CreateTaxDTO
    {
        $validated = $this->validated();

        $name = Arr::get($validated, 'name');
        $value = Arr::get($validated, 'value');
        $isPercent = Arr::get($validated, 'is_percent');
        $endSubtraction = Arr::get($validated, 'end_subtraction');

        return new CreateTaxDTO(
            $name,
            $value,
            $isPercent,
            $endSubtraction
        );
    }
}
