<?php

namespace App\Http\Requests\TimeTrack;

use App\DTO\TimeTrack\GetReportDTO;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetReportRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'group_id'  => ['required', 'numeric', 'exists:profile_groups,id'],
            'year'      => ['required', 'numeric'],
            'month'     => ['required', 'numeric'],
            'day'       => ['required', 'numeric']
        ];
    }

    /**
     * @return GetReportDTO
     */
    public function toDto(): GetReportDTO
    {
        $validated = $this->validated();

        $groupId = Arr::get($validated, 'group_id');
        $year    = Arr::get($validated, 'year');
        $month   = Arr::get($validated, 'month');
        $day     = Arr::get($validated, 'day');

        return new GetReportDTO(
            $groupId,
            $year,
            $month,
            $day
        );
    }
}
