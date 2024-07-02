<?php

namespace App\Http\Requests\TimeTrack;

use App\DTO\TimeTrack\DeleteSettingDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DeleteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => ['required', 'numeric']
        ];
    }

    /**
     * @return DeleteSettingDTO
     */
    public function toDto(): DeleteSettingDTO
    {
        $validated = $this->validated();

        $positionId = Arr::get($validated, 'id');

        return new DeleteSettingDTO($positionId);
    }
}