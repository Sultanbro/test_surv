<?php

namespace App\Http\Requests\Group;

use App\DTO\Group\RestoreGroupDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class RestoreGroupRequest extends FormRequest
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
            'id' => ['required', 'numeric', 'exists:profile_groups,id']
        ];
    }

    /**
     * @return RestoreGroupDTO
     */
    public function toDto(): RestoreGroupDTO
    {
        $validated = $this->validated();
        $id = Arr::get($validated, 'id');

        return new RestoreGroupDTO($id);
    }
}
