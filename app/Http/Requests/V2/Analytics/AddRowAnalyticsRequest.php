<?php

namespace App\Http\Requests\V2\Analytics;

use App\DTO\Analytics\V2\AddRowAnalyticsDto;
use Illuminate\Foundation\Http\FormRequest;

class AddRowAnalyticsRequest extends FormRequest
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
            'group_id'  => 'required|integer|exists:profile_groups,id',
            'rows'      => 'required|array',
            'rows.*.name'   => 'required|string',
            'rows.*.index'  => 'required|integer',
            'rows.*.type'   => 'required|string',
            'year'      => 'required|integer',
            'month'     => 'required|integer'
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
