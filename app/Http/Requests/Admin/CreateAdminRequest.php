<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\AddAdminDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class CreateAdminRequest extends FormRequest
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
            'name'      => ['required', 'string', 'max:190'],
            'last_name' => ['required', 'string', 'max:190'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
            'image'     => ['file', 'mimes:jpg,png', 'max:7168'],
            'phone'     => ['required', 'string']
        ];
    }

    /**
     * @return AddAdminDTO
     */
    public function toDto(): AddAdminDTO
    {
        $validated = $this->validated();

        $name       = Arr::get($validated, 'name');
        $lastName   = Arr::get($validated, 'last_name');
        $email      = Arr::get($validated, 'email');
        $password   = Arr::get($validated, 'password');
        $roleId     = Arr::get($validated, 'role_id');
        $image      = Arr::get($validated, 'image');
        $phone      = Arr::get($validated, 'phone');

        return new AddAdminDTO(
            $name,
            $lastName,
            $email,
            $password,
            $roleId,
            $image,
            $phone
        );

    }
}
