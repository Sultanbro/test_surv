<?php

namespace App\Http\Requests\V2\Analytics;

use App\DTO\Analytics\V2\GetRentabilityDto;
use Illuminate\Foundation\Http\FormRequest;

class GetRentabilityRequest extends FormRequest
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
            'month' => 'required|integer',
            'year'  => 'required|integer'
        ];
    }

    /**
     * @return GetRentabilityDto
     */
    public function toDto(): GetRentabilityDto
    {
        return GetRentabilityDto::fromArray($this->validated());
    }
}
