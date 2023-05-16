<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\UpdateWorkChartDTO;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class UpdateWorkChartRequest extends BaseWorkChartRequest
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
        $rules = [
            'name' => ['string'],
            'start_time' => ['string'],
            'end_time' => ['string'],
            'work_charts_type' => 'integer|exists:work_chart_type_rbs,id',
        ];

        $work_charts_type = $this->input('work_charts_type');
        if($work_charts_type === 1){
            $rules += [
                'usual_schedule' => ['required', 'string', 'max_digits:7']
            ];
            return $rules;
        }

        $rules += [
            'chart_workdays' => ['required', 'integer', 'min:1', 'max:30'],
            'chart_dayoffs' => ['required', 'integer', 'min:1', 'max:30'],
            'size:'.($this->input('chart_workdays') + $this->input('chart_dayoffs')).':30'
        ];
        return $rules;
    }

    /**
     * @return UpdateWorkChartDTO
     */
    public function toDto(int $id): UpdateWorkChartDTO
    {
        $validated = $this->validated();

        $name       = Arr::get($validated, 'name');
        $startTime    = Arr::get($validated, 'start_time');
        $endTime    = Arr::get($validated, 'end_time');
        $chartWorkType  = (int) Arr::get($validated, 'work_charts_type');

        if ($chartWorkType === 1){
            $usualSchedule = bindec((string)Arr::get($validated, 'usual_schedule'));

            return new UpdateWorkChartDTO(
                id: $id,
                name:$name,
                startTime: $startTime,
                endTime: $endTime,
                chartWorkType: $chartWorkType,
                chartWorkdays: 0,
                chartDayoffs: 0,
                usualSchedule: $usualSchedule,
            );
        }

        $chartWorkdays  = (int) Arr::get($validated, 'chart_workdays');
        $chartDayoffs  = (int) Arr::get($validated, 'chart_dayoffs');

        return new UpdateWorkChartDTO(
            id: $id,
            name: $name,
            startTime: $startTime,
            endTime: $endTime,
            chartWorkType: $chartWorkType,
            chartWorkdays: $chartWorkdays,
            chartDayoffs: $chartDayoffs,
            usualSchedule: 0,
        );
    }
}
