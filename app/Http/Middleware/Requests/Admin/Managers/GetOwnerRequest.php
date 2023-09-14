<?php

namespace App\Http\Requests\Admin\Managers;

use App\DTO\Manager\GetOwnerDTO;
use App\DTO\Manager\PutManagerToOwnerDTO;
use App\Rules\Admin\CheckManager;
use App\Rules\Admin\CheckOwner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetOwnerRequest extends FormRequest
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
            'manager_id' => ['required', new CheckManager]
        ];
    }

    /**
     * @return GetOwnerDTO
     */
    public function toDto(): GetOwnerDTO
    {
        $validated = $this->validated();

        $managerId = Arr::get($validated, 'manager_id');

        return new GetOwnerDTO(
            $managerId
        );
    }
}
