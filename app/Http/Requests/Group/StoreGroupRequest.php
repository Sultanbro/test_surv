<?php

namespace App\Http\Requests\Group;

use App\DTO\Group\StoreGroupDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreGroupRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:64']
        ];
    }

    /**
     * @return StoreGroupDTO
     */
    public function toDto(): StoreGroupDTO
    {
        $transfer = $this->validated();

        $name = Arr::get($transfer, 'name');

        return new StoreGroupDTO($name);
    }
}
