<?php

namespace App\Http\Requests\Position;

use App\DTO\Position\AnyPositionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StorePositionRequest extends FormRequest
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
            'position' => ['required', 'string'],
            'is_head' => ['nullable|boolean']
        ];
    }

    /**
     * @return AnyPositionDTO
     */
    public function toDto(): AnyPositionDTO
    {
        $validated = $this->validated();

        $position = Arr::get($validated, 'position');
        $is_head = Arr::get($validated, 'is_head');

        return new AnyPositionDTO($position,$is_head);
    }
}
