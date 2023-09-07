<?php

namespace App\Http\Requests\Permissions;

use App\DTO\Permissions\SwitchAccessDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SwitchAccessRequest extends FormRequest
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
            'accesses'      => ['required', 'array'],
            'user_id'       => ['required', 'exists:users,id'],
            'accesses.*.id'     => ['numeric', 'exists:permissions,id'],
            'accesses.*.name'   => ['string', 'exists:permissions,name'],
            'accesses.*.is_access' => ['bool'],
        ];
    }

    /**
     * @return SwitchAccessDTO
     */
    public function toDto(): SwitchAccessDTO
    {
        $validated = $this->validated();

        $accesses = Arr::get($validated, 'accesses');
        $userId = Arr::get($validated, 'user_id');

        return new SwitchAccessDTO(
            accesses: $accesses,
            userId: $userId
        );
    }
}
