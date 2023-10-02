<?php

namespace App\Http\Requests\Position;

use App\DTO\Position\GetPositionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetPositionRequest extends FormRequest
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
            'name' => ['required', 'numeric', 'exists:position,id']
        ];
    }

    /**
     * @return GetPositionDTO
     */
    public function toDto(): GetPositionDTO
    {
        $validated = $this->validated();

        $id = Arr::get($validated, 'name');

        return new GetPositionDTO($id);
    }
}
