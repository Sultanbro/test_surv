<?php

namespace App\Http\Requests\Position;

use App\DTO\Position\AnyPositionDTO;
use App\DTO\Position\DeletePositionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DeletePositionRequest extends FormRequest
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
    public function rules()
    {
        return [
            'position' => 'required|integer|exists:position,id'
        ];
    }


    public function toDto()
    {
        $validated = $this->validated();

        $position = Arr::get($validated, 'position');

        return new DeletePositionDTO($position);
    }
}
