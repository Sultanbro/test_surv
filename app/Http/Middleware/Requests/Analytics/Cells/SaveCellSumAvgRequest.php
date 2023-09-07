<?php

namespace App\Http\Requests\Analytics\Cells;

use App\DTO\Analytics\Cells\SaveCellSumAvgDTO;
use App\DTO\Analytics\Cells\SaveCellTimeDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SaveCellSumAvgRequest extends FormRequest
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
            'row_id'    => 'required|numeric|exists:analytic_rows,id',
            'column_id' => 'required|numeric|exists:analytic_columns,id',
            'class'     => 'required|string',
            'year'      => 'required|numeric',
            'month'     => 'required|numeric'
        ];
    }

    /**
     * @return SaveCellSumAvgDTO
     */
    public function toDto(): SaveCellSumAvgDTO
    {
        $validated = $this->validated();

        $groupId    = Arr::get($validated, 'group_id');
        $rowId      = Arr::get($validated, 'row_id');
        $columnId   = Arr::get($validated, 'column_id');
        $class      = Arr::get($validated, 'class');
        $year       = Arr::get($validated, 'year');
        $month      = Arr::get($validated, 'month');

        return new SaveCellSumAvgDTO(
            $groupId,
            $rowId,
            $columnId,
            $class,
            $year,
            $month
        );
    }
}
