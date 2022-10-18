<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\User;

class UserProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'     => ['unique:users,email'],
            'currency'  => Rule::in(User::CURRENCY)
        ];
    }

    public function messages()
    {
        $email = User::findOrFail(auth()->id())->email;
        return [
            'email.unique' => 'Введенный E-mail уже занят: ' . $email
        ];
    }
}
