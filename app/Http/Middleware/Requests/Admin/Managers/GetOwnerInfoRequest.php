<?php

namespace App\Http\Requests\Admin\Managers;

use App\DTO\Manager\GetOwnerDTO;
use App\DTO\Manager\GetOwnerInfoDTO;
use App\Rules\Admin\CheckOwner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetOwnerInfoRequest extends FormRequest
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
            'owner_id' => ['required', new CheckOwner]
        ];
    }

    /**
     * @return GetOwnerInfoDTO
     */
    public function toDto(): GetOwnerInfoDTO
    {
        $validated = $this->validated();

        $managerId = Arr::get($validated, 'owner_id');

        return new GetOwnerInfoDTO(
            $managerId
        );
    }
}
