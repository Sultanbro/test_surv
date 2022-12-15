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
            'id'            => ['required', 'numeric'],
            'new_name'      => ['string'],
            'indexation'    => ['numeric'],
            'sum'           => ['numeric'],
            'desc'          => ['array'],
            'desc.require'  => ['string', 'min:0', 'max:255'],
            'desc.actions'  => ['string', 'min:0', 'max:255'],
            'desc.time'     => ['string', 'min:0', 'max:255'],
            'desc.salary'   => ['string', 'min:0', 'max:255'],
            'desc.knowledge' => ['string', 'min:0', 'max:255'],
            'desc.next_step' => ['string', 'min:0', 'max:255'],
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

        return new StorePositionWithDescriptionDTO(
            $id,
            $newName,
            $indexation,
            $sum,
            $description
        );
    }
}
