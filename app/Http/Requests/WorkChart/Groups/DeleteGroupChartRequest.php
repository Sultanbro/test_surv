<?php

namespace App\Http\Requests\WorkChart\Groups;

use App\DTO\WorkChart\Groups\DeleteGroupChartDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DeleteGroupChartRequest extends FormRequest
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
            'group_id' => 'required|integer|exists:profile_groups,id'
        ];
    }

    /**
     * @return DeleteGroupChartDTO
     */
    public function toDto(): DeleteGroupChartDTO
    {
        $validated = $this->validated();

        $userId = Arr::get($validated, 'group_id');

        return new DeleteGroupChartDTO($userId);
    }
}
