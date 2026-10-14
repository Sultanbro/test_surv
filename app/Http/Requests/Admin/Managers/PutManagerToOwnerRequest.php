<?php

namespace App\Http\Requests\Admin\Managers;

use App\DTO\Manager\PutManagerToOwnerDTO;
use App\Rules\Admin\CheckManager;
use App\Rules\Admin\CheckOwner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class PutManagerToOwnerRequest extends FormRequest
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
            'owner_id'   => ['required', new CheckOwner],
            'manager_id' => ['required', new CheckManager]
        ];
    }

    /**
     * @return PutManagerToOwnerDTO
     */
    public function toDto(): PutManagerToOwnerDTO
    {
        $validated = $this->validated();

        $ownerId = Arr::get($validated, 'owner_id');
        $managerId = Arr::get($validated, 'manager_id');

        return new PutManagerToOwnerDTO(
            $ownerId,
            $managerId
        );
    }
}
