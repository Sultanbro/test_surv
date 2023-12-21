<?php

namespace App\Http\Requests\V2\Analytics;

use App\DTO\Analytics\V2\AddRowAnalyticsDto;
use Illuminate\Foundation\Http\FormRequest;

class AddRowRequest extends FormRequest
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
            'group_id'          => 'required|integer',
            'after_row_id'      => 'required|integer',
            'date'              => 'required|string'
        ];
    }

    /**
     * @return AddRowAnalyticsDto
     */
    public function toDto(): AddRowAnalyticsDto
    {
        return AddRowAnalyticsDto::fromArray($this->validated());
    }
}
