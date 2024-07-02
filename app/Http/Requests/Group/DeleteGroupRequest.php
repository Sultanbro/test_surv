<?php

namespace App\Http\Requests\Group;

use App\DTO\Group\DeleteGroupDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DeleteGroupRequest extends FormRequest
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
            'group' => ['required', 'numeric', 'exists:profile_groups,id']
        ];
    }

    /**
     * @return DeleteGroupDTO
     */
    public function toDto(): DeleteGroupDTO
    {
        $validated = $this->validated();

        $id = Arr::get($validated, 'group');

        return  new DeleteGroupDTO($id);
    }
}
