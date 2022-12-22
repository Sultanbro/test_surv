<?php

namespace App\Http\Requests\TimeTrack;

use App\DTO\TimeTrack\SettingDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetSettingRequest extends FormRequest
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
            'tab' => ['required', 'numeric', 'min:1', 'max:7']
        ];
    }

    public function toDto(): SettingDTO
    {
        $transferData = $this->validated();

        $tabId = Arr::get($transferData, 'tab');

        return SettingDTO::toArray($tabId);
    }
}