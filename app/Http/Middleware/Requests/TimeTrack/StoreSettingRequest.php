<?php

namespace App\Http\Requests\TimeTrack;

use App\DTO\TimeTrack\StoreSettingDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreSettingRequest extends FormRequest
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
            'position' => ['required', 'string']
        ];
    }

    /**
     * @return StoreSettingDTO
     */
    public function toDto(): StoreSettingDTO
    {
        $validated = $this->validated();

        $position = Arr::get($validated, 'position');

        return new StoreSettingDTO($position);
    }
}