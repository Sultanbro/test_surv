<?php

namespace App\Http\Requests\TimeTrack;

use Illuminate\Foundation\Http\FormRequest;

class StartOrStopTrackingRequest extends FormRequest
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
            'start' => 'required_without:stop|date_format:H:i:s',
            'stop' => 'required_without:start|date_format:H:i:s',
        ];
    }
}