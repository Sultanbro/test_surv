<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\UpdateAdminDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class EditAdminRequest extends FormRequest
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
            'name'      => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:users',
            'phone'     => 'sometimes|nullable',
            'password'  => 'sometimes|nullable|string|min:8'
        ];
    }

    /**
     * @return UpdateAdminDTO
     */
    public function toDto(): UpdateAdminDTO
    {
        $validated = $this->validated();

        $name       = Arr::get($validated, 'name');
        $lastName   = Arr::get($validated, 'last_name');
        $email      = Arr::get($validated, 'email');
        $phone      = Arr::get($validated, 'phone');
        $password   = Arr::get($validated, 'password');

        return new UpdateAdminDTO(
            $name,
            $lastName,
            $email,
            $phone,
            $password
        );
    }
}
