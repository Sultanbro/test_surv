<?php

namespace App\Http\Requests\Fine;

use App\DTO\BaseDTO;
use App\DTO\Fine\UpdateUserFinesDTO;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Arr;

class UpdateUserFinesRequest extends BaseFormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'fines' => ['array'],
            'date' => ['required', 'string'],
            'comment' => ['required', 'string'],
        ];
    }

    /**
     * @return BaseDTO
     */
    public function toDto(): UpdateUserFinesDTO
    {
        $validated = $this->validated();

        $user_id = Arr::get($validated, 'user_id');
        $date = Arr::get($validated, 'date');
        $comment = Arr::get($validated, 'comment');
        $fines = Arr::get($validated, 'fines');

        return new UpdateUserFinesDTO(
            $user_id,
            $fines,
            $date,
            $comment
        );
    }
}
