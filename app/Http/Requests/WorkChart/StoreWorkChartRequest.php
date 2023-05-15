<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

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
        $rules = [
            'name' => ['required', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'work_charts_type' => 'integer|exists:work_chart_type_rbs,id',
        ];

        if($this->input('work_charts_type') === 1){
            $rules += [
                'chart_workdays' => ['required', 'integer', 'min:1', 'max:6'],
                'chart_dayoffs' => ['required', 'integer', 'min:1', 'max:6'],
            ];

            if ($this->input('chart_workdays') + $this->input('chart_dayoffs') > 7){
                throw ValidationException::withMessages(['work_charts_type_error' => 'Сумма work_chart_work и wor_chart_rest должна быть равна 7']);
            }
        }
        else if($this->input('work_charts_type') === 2){
            $rules += [
                'chart_workdays' => ['required', 'integer', 'min:1', 'max:30'],
                'chart_dayoffs' => ['required', 'integer', 'min:1', 'max:30'],
            ];

            if ($this->input('chart_workdays') + $this->input('chart_dayoffs') > 30){
                throw ValidationException::withMessages(['work_charts_type_error' => 'Сумма work_chart_work и wor_chart_rest должна быть равна 30']);
            }
        }
        else {
            $rules += [
                'chart_workdays' => ['required', 'integer', 'min:1', 'max:6'],
                'chart_dayoffs' => ['required', 'integer', 'min:1', 'max:6'],
            ];
        }
        return $rules;
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
        $chartWorkType  = (int) Arr::get($validated, 'work_charts_type');

        return new StoreWorkChartDTO(
            $name,
            $chartWorkdays,
            $chartDayoffs,
            $startTime,
            $endTime,
            $chartWorkType,
        );
    }
}
