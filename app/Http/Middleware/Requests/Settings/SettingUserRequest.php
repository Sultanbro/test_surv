<?php

namespace App\Http\Requests\Settings;

use App\DTO\Settings\CreateUserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SettingUserRequest extends FormRequest
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
            'id' => ['numeric', 'exists:users,id']
        ];
    }

    /**
     * @return CreateUserDTO
     */
    public function toDto(): CreateUserDTO
    {
        $validated = $this->validated();

        $id = Arr::get($validated, 'id');

        return new CreateUserDTO($id);
    }
}
