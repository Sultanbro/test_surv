<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\UpdateWorkChartDTO;
use Illuminate\Support\Arr;

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
        return [
            'name' => ['string'],
            'chart_workdays' => ['required_with:chart_dayoffs', 'integer', 'min:1', 'max:6'],
            'chart_dayoffs' => ['required_with:chart_workdays', 'integer', 'min:1', 'max:6'],
            'start_time' => ['string'],
            'end_time' => ['string'],
        ];
    }

    /**
     * @return UpdateWorkChartDTO
     */
    public function toDto(int $id): UpdateWorkChartDTO
    {
        $validated = $this->validated();

        $name       = Arr::get($validated, 'name');
        $chartWorkdays  = (int) Arr::get($validated, 'chart_workdays');
        $chartDayoffs  = (int) Arr::get($validated, 'chart_dayoffs');
        $startTime    = Arr::get($validated, 'start_time');
        $endTime    = Arr::get($validated, 'end_time');

        return new UpdateWorkChartDTO(
            $id,
            $name,
            $chartWorkdays,
            $chartDayoffs,
            $startTime,
            $endTime,
        );
    }
}
