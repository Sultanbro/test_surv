<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreWorkChartRequest extends FormRequest
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
            'name'    => ['required', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string']
        ];
    }

    /**
     * @return StoreWorkChartDTO
     */
    public function toDto(): StoreWorkChartDTO
    {
        $validated = $this->validated();

        $name       = Arr::get($validated, 'name');
        $startTime  = Arr::get($validated, 'start_time');
        $endTime    = Arr::get($validated, 'end_time');

        return new StoreWorkChartDTO(
            $name,
            $startTime,
            $endTime
        );
    }
}
