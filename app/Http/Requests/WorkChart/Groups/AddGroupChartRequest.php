<?php

namespace App\Http\Requests\WorkChart\Groups;

use App\DTO\WorkChart\Groups\AddGroupChartDTO;
use App\DTO\WorkChart\User\AddUserChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class AddGroupChartRequest extends FormRequest
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
            'group_id' => 'required|integer|exists:profile_groups,id',
            'work_chart_id' => 'required|integer|exists:work_charts,id'
        ];
    }

    /**
     * @return AddGroupChartDTO
     */
    public function toDto(): AddGroupChartDTO
    {
        $validated = $this->validated();

        $groupId = Arr::get($validated, 'group_id');
        $workChartId = Arr::get($validated, 'work_chart_id');

        return new AddGroupChartDTO(
            $groupId,
            $workChartId
        );
    }
}
