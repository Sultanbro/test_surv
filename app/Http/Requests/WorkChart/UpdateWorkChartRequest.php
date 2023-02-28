<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\UpdateWorkChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateWorkChartRequest extends FormRequest
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
            'name'    => ['string'],
            'start_time' => ['string'],
            'end_time' => ['string']
        ];
    }

    /**
     * @return UpdateWorkChartDTO
     */
    public function toDto(): UpdateWorkChartDTO
    {
        $validated = $this->validated();

        $name       = Arr::get($validated, 'name');
        $startTime    = Arr::get($validated, 'start_time');
        $endTime    = Arr::get($validated, 'end_time');

        return new UpdateWorkChartDTO(
            $name,
            $startTime,
            $endTime
        );
    }
}
