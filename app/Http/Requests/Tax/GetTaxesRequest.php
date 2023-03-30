<?php

namespace App\Http\Requests\Tax;

use App\DTO\Tax\GetTaxesDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetTaxesRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id'
        ];
    }

    /**
     * @return GetTaxesDTO
     */
    public function toDto(): GetTaxesDTO
    {
        $validated = $this->validated();

        $userId = Arr::get($validated, 'user_id');

        return new GetTaxesDTO($userId);
    }
}
