<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\UpdateWorkChartDTO;
use App\Models\WorkChart\WorkChartModel;
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
            'name' => ['required', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'work_charts_type' => 'integer|exists:work_chart_type_rbs,id|in:1,2',
            'usual_schedule' => $this->getUsualScheduleRule(),
            'chart_workdays' => $this->getChartWorkdaysRule(),
            'chart_dayoffs' => $this->getChartDayoffsRule(),
        ];

        if ($this->input('chart_workdays') + $this->input('chart_dayoffs') > 30){
            throw ValidationException::withMessages(['work_charts_type_error' => 'Сумма work_chart_work и wor_chart_rest должна быть равна 30']);
        }

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
            $usualSchedule = Arr::get($validated, 'usual_schedule');
            $usualSchedule_dec = bindec((string)$usualSchedule);

            return new UpdateWorkChartDTO(
                id: $id,
                name:$name,
                startTime: $startTime,
                endTime: $endTime,
                chartWorkType: $chartWorkType,
                chartWorkdays: substr_count($usualSchedule, 1),
                chartDayoffs: substr_count($usualSchedule, 0),
                usualSchedule: $usualSchedule_dec,
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

    private function getUsualScheduleRule()
    {
        return $this->input('work_charts_type') == 1 ? 'required' : 'nullable';
    }

    private function getChartWorkdaysRule()
    {
        return $this->input('work_charts_type') == 2 ? 'required' : 'nullable';
    }

    private function getChartDayoffsRule()
    {
        return $this->input('work_charts_type') == 2 ? 'required' : 'nullable';
    }
}
