<?php

namespace App\Http\Requests\Position;

use App\DTO\Position\StorePositionWithDescriptionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StorePositionWithDescriptionRequest extends FormRequest
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
            'id'            => ['required', 'numeric', 'exists:position,id'],
            'new_name'      => ['string'],
            'indexation'    => ['numeric'],
            'sum'           => ['numeric'],
            'desc'          => ['array'],
            'is_head'      => ['boolean'],
            'desc.require'  => ['nullable', 'min:3'],
            'desc.actions'  => ['nullable', 'min:3'],
            'desc.time'     => ['nullable', 'min:3'],
            'desc.salary'   => ['nullable', 'min:3'],
            'desc.knowledge' => ['nullable', 'min:3'],
            'desc.next_step' => ['nullable', 'min:3'],
            'desc.show'      => ['boolean'],
        ];
    }

    /**
     * @return StorePositionWithDescriptionDTO
     */
    public function toDto(): StorePositionWithDescriptionDTO
    {
        $validated = $this->validated();

        $id = Arr::get($validated, 'id');
        $newName = Arr::get($validated, 'new_name');
        $indexation = Arr::get($validated, 'indexation');
        $sum = Arr::get($validated, 'sum');
        $description = Arr::get($validated, 'desc');
        $is_head = Arr::get($validated, 'is_head');

        return new StorePositionWithDescriptionDTO(
            $id,
            $newName,
            $indexation,
            $sum,
            $description,
            $is_head
        );
    }
}
