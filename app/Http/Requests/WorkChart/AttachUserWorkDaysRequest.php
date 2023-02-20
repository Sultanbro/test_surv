<?php

namespace App\Http\Requests\WorkChart;

use App\DTO\WorkChart\AttachUserWorkDaysDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AttachUserWorkDaysRequest extends FormRequest
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
            'user_id'   => 'required|integer|exists:users,id',
            'workdays'  => 'required|array',
            'workdays.*' => Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
        ];
    }

    /**
     * @return AttachUserWorkDaysDTO
     */
    public function toDto(): AttachUserWorkDaysDTO
    {
        $validated = $this->validated();

        $userId = Arr::get($validated, 'user_id');
        $weekdays = Arr::get($validated, 'workdays');

        return new AttachUserWorkDaysDTO(
            $userId,
            $weekdays
        );
    }
}
