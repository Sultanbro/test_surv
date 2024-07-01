<?php

namespace App\Http\Requests\Analytics\Cells;

use App\DTO\Analytics\Cells\SaveCellActivityDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SaveCellActivityRequest extends FormRequest
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
            'group_id'  => 'required|numeric|exists:profile_groups,id',
            'row_id'    => 'required|numeric',
            'activity_id' => 'required|numeric|exists:activities,id',
            'class'     => 'required|string',
            'year'      => 'required|numeric',
            'month'     => 'required|numeric'
        ];
    }

    /**
     * @return SaveCellActivityDTO
     */
    public function toDto(): SaveCellActivityDTO
    {
        $validated = $this->validated();

        $groupId = Arr::get($validated, 'group_id');
        $rowId = Arr::get($validated, 'row_id');
        $activityId = Arr::get($validated, 'activity_id');
        $class = Arr::get($validated, 'class');
        $year = Arr::get($validated, 'year');
        $month = Arr::get($validated, 'month');

        return new SaveCellActivityDTO(
            $groupId,
            $rowId,
            $activityId,
            $class,
            $year,
            $month
        );
    }
}
