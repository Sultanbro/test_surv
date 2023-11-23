<?php

namespace App\Http\Requests\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticPositionsDto;
use Illuminate\Foundation\Http\FormRequest;

class GetAnalyticPositionsRequest extends FormRequest
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
            'group_id' => 'required|integer|exists:profile_groups,id'
        ];
    }

    /**
     * @return GetAnalyticPositionsDto
     */
    public function toDto(): GetAnalyticPositionsDto
    {
        return GetAnalyticPositionsDto::fromArray($this->validated());
    }
}
