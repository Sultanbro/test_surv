<?php

namespace App\Http\Requests;

use App\DTO\TimeTrackSettingDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class TimeTrackSettingRequest extends FormRequest
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
            'tabId' => ['required', 'numeric', 'min:1', 'max:7']
        ];
    }

    public function toDto(): TimeTrackSettingDTO
    {
        $transferData = $this->validated();

        $tabId = Arr::get($transferData, 'tabId');

        return TimeTrackSettingDTO::toArray($tabId);
    }
}
