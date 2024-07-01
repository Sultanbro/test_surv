<?php

namespace App\Http\Requests\Trigger;

use App\DTO\BaseDTO;
use App\DTO\Triggers\ApplyEmployeeDTO;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ApplyEmployeeRequest extends BaseFormRequest
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
            'user_id' => 'required|integer|exists:users,id'
        ];
    }

    /**
     * @return BaseDTO<ApplyEmployeeDTO>
     */
    public function toDto(): BaseDTO
    {
        $validated = $this->validated();

        $userId = Arr::get($validated, 'user_id');

        return new ApplyEmployeeDTO(
            $userId
        );
    }
}
