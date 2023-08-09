<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use App\Models\WorkChart\WorkChartModel;
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
            'name' => ['required', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'work_charts_type' => $this->getWorkChartTypeRule(),
            'usual_schedule' => $this->getUsualScheduleRule(),
            'chart_workdays' => $this->getChartWorkdaysRule(),
            'chart_dayoffs' => $this->getChartDayoffsRule(),
            'rest_time' => ['nullable', 'integer', 'max:10000'],
            'floating_dayoffs' => ['nullable', 'integer', 'max:100'],
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
        $chartWorkType  = (int) Arr::get($validated, 'work_charts_type');
        $usualSchedule = Arr::get($validated, 'usual_schedule');
        $chartWorkdays  = (int) Arr::get($validated, 'chart_workdays');
        $chartDayoffs  = (int) Arr::get($validated, 'chart_dayoffs');
        $rest_time  = (int) Arr::get($validated, 'rest_time');
        $floatingDayoffs = (int) Arr::get($validated, 'floating_dayoffs');

        return new StoreWorkChartDTO(
            name:$name,
            startTime: $startTime,
            endTime: $endTime,
            chartWorkType: $chartWorkType,
            chartWorkdays: $chartWorkType === WorkChartModel::WORK_CHART_TYPE_USUAL
                ? $floatingDayoffs
                    ? 7 - $floatingDayoffs
                    : substr_count($usualSchedule, 1)
                : $chartWorkdays,
            chartDayoffs: $chartWorkType === WorkChartModel::WORK_CHART_TYPE_USUAL
                ? $floatingDayoffs
                    ? $floatingDayoffs
                    : substr_count($usualSchedule, 0)
                : $chartDayoffs,
            usualSchedule: bindec((string)$usualSchedule),
            restTime: $rest_time,
            floatingDayoffs: $floatingDayoffs
        );
    }
}
