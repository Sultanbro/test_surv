<?php

namespace App\Http\Requests\TimeTrack\SalaryWorkChart;

use App\DTO\TimeTrack\SalaryWorkChartDTO\SalaryWorkChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SalaryWorkChartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'month'    => ['required', 'string'],
            'year' => ['required', 'string'],
            'start_day' => ['required', 'string'],
            'hollidays'  => ['json'],
        ];
    }

    /**
     * @return SalaryWorkChartDTO
     */
    public function toDto(): SalaryWorkChartDTO
    {
        $validated = $this->validated();

        $month      = Arr::get($validated, 'month');
        $year       = Arr::get($validated, 'year');
        $startDay   = Arr::get($validated, 'start_day');
        $hollidays  = Arr::get($validated, 'hollidays');

        return new SalaryWorkChartDTO(
            $month,
            $year,
            $startDay,
            $hollidays,
        );
    }
}
