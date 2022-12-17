<?php

namespace App\Http\Requests\GroupUser;

use Illuminate\Foundation\Http\FormRequest;

class SaveUsersRequest extends FormRequest
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
            'group_id'  => ['required', 'numeric', 'exists:profile_groups,id'],
            'users'     => ['required', 'array'],
            'users.id'  => ['numeric', 'exists:users,id'],
            'corp_books'    => ['required', 'array'],
            'corp_books.id' => ['numeric', 'exists:books,id'],
            'group_info'    => ['required', 'array'],
            'group_info.*'  => ['required_with:group_info'],
            'dialer_id'     => ['numeric'],
            'script_id'     => ['numeric'],
            'talk_hours'    => ['numeric'],
            'talk_minutes'  => ['numeric']
        ];
    }
}
