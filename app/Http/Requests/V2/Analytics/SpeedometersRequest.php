<?php

namespace App\Http\Requests\V2\Analytics;

use App\DTO\Analytics\V2\SpeedometerDto;
use Illuminate\Foundation\Http\FormRequest;

class SpeedometersRequest extends FormRequest
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
            'gauge' => 'required|array',
            'type'  => 'required|integer'
        ];
    }

    /**
     * @return SpeedometerDto
     */
    public function toDto(): SpeedometerDto
    {
        return SpeedometerDto::fromArray($this->validated());
    }
}
