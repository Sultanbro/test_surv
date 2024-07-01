<?php

namespace App\Http\Requests\V2\Analytics;

use App\DTO\Analytics\V2\EditActivityDto;
use Illuminate\Foundation\Http\FormRequest;

class EditActivityRequest extends FormRequest
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
            'activity'      => 'required|array',
            'employees'     => 'array',
            'employees.*'   => 'required|exists:users,id',
            'year'          =>  'required|integer',
            'month'         => 'required|integer'
        ];
    }

    /**
     * @return EditActivityDto
     */
    public function toDto(): EditActivityDto
    {
        return EditActivityDto::fromArray($this->validated());
    }
}
