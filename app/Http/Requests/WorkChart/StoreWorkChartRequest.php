<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
            'chart_workdays'   => ['required', 'integer', 'min:1', 'max:6'],
            'chart_dayoffs'   => ['required', 'integer', 'min:1', 'max:6'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string']
        ];
    }

    private static readonly int $MAX_CHART_DAYS = 7;

    public function validated() {
        $validated = parent::validated();

        $chartWorkdays  = (int) Arr::get($validated, 'chart_workdays');
        $chartDayoffs  = (int) Arr::get($validated, 'chart_dayoffs');

        if ($chartWorkdays + $chartDayoffs > self::$MAX_CHART_DAYS) {
            throw new BadRequestHttpException('max chart days sum is '. self::$MAX_CHART_DAYS);
        }

        return $validated;
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
