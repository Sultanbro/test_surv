<?php

namespace App\Http\Requests\Settings\WorkChartRequest;

use App\DTO\Settings\WorkChartDTO\UpdateWorkChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateWorkChartRequest extends FormRequest
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
            'name'    => ['required', 'string'],
            'time_beg' => ['required', 'string'],
            'time_end' => ['required', 'string'],
            'day_off'  => ['required','array'],
        ];
    }

    /**
     * @return UpdateWorkChartDTO
     */
    public function toDto(): UpdateWorkChartDTO
    {
        $validated = $this->validated();

        $name       = Arr::get($validated, 'name');
        $timeBeg    = Arr::get($validated, 'time_beg');
        $timeEnd    = Arr::get($validated, 'time_end');
        $dayOff     = Arr::get($validated, 'day_off');

        return new UpdateWorkChartDTO(
            $name,
            $timeBeg,
            $timeEnd,
            $dayOff,
        );
    }
}
