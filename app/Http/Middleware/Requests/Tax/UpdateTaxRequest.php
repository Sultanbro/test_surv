<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\UpdateTaxDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateTaxRequest extends FormRequest
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
            'id'         => 'required|integer|exists:taxes,id',
            'name'       => 'string',
            'value'      => 'regex:/^\d*(\.\d{2})?$/',
            'is_percent' => 'bool',
            'user_id'    => 'required|integer|exists:users,id'
        ];
    }

    /**
     * @return UpdateTaxDTO
     */
    public function toDto(): UpdateTaxDTO
    {
       $validated   = $this->validated();

        $id         = Arr::get($validated, 'id');
        $name       = Arr::get($validated, 'name');
        $value      = Arr::get($validated, 'value');
        $isPercent  = Arr::get($validated, 'is_percent');
        $userId     = Arr::get($validated, 'user_id');

        return new UpdateTaxDTO(
            $id,
            $name,
            $value,
            $isPercent,
            $userId
        );
    }
}
