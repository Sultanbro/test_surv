<?php

namespace App\Http\Requests\WorkChart\Users;

use App\DTO\WorkChart\User\AddUserChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class AddUserChartRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'work_chart_id' => 'required|integer|exists:work_charts,id'
        ];
    }

    /**
     * @return AddUserChartDTO
     */
    public function toDto(): AddUserChartDTO
    {
        $validated = $this->validated();

        $userId = Arr::get($validated, 'user_id');
        $workChartId = Arr::get($validated, 'work_chart_id');

        return new AddUserChartDTO(
            $userId,
            $workChartId
        );
    }
}
