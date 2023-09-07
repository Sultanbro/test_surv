<?php

namespace App\Http\Requests\GroupUser;

use App\DTO\GroupUser\GetUsersDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetUsersRequest extends FormRequest
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
            'id' => ['numeric', 'nullable']
        ];
    }

    /**
     * @return GetUsersDTO
     */
    public function toDto(): GetUsersDTO
    {
        $validated = $this->validated();
        $id = Arr::get($validated, 'id');

        return new GetUsersDTO($id);
    }
}
