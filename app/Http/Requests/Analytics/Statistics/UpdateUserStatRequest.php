<?php

namespace App\Http\Requests\Analytics\Statistics;

use App\DTO\Analytics\Statistics\UpdateUserStatDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateUserStatRequest extends FormRequest
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
            'id'            => 'required|integer|exists:activities,id',
            'group_id'      => 'required|integer|exists:profile_groups,id',
            'employee_id'   => 'required|integer|exists:users,id',
            'value'     => 'string',
            'year'      => 'required|integer',
            'month'     => 'required|integer',
            'day'       => 'required|integer'
        ];
    }

    /**
     * @return UpdateUserStatDTO
     */
    public function toDto(): UpdateUserStatDTO
    {
        $validated = $this->validated();

        $activityId = Arr::get($validated, 'id');
        $groupId = Arr::get($validated, 'group_id');
        $employeeId = Arr::get($validated, 'employee_id');
        $value = Arr::get($validated, 'value');
        $year = Arr::get($validated, 'year');
        $month = Arr::get($validated, 'month');
        $day = Arr::get($validated, 'day');

        return new UpdateUserStatDTO(
            $activityId,
            $groupId,
            $employeeId,
            $value,
            $year,
            $month,
            $day
        );
    }
}
