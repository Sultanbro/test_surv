<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use Illuminate\Support\Arr;

class StoreWorkChartRequest extends BaseWorkChartRequest
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
            'chart_workdays'   => ['required', 'integer', 'min:1', 'max:6'],
            'chart_dayoffs'   => ['required', 'integer', 'min:1', 'max:6'],
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
        $chartWorkdays  = (int) Arr::get($validated, 'chart_workdays');
        $chartDayoffs  = (int) Arr::get($validated, 'chart_dayoffs');

        return new StoreWorkChartDTO(
            $name,
            $chartWorkdays,
            $chartDayoffs,
            $startTime,
            $endTime,
        );
    }
}
