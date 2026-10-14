<?php

namespace App\Http\Requests;

use App\DayType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
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
            'user_id'      => 'required|numeric',
            'date'         => 'required|string',
            'type'         => ['required', 'numeric', Rule::in([DayType::DAY_TYPES['TRAINEE'], DayType::DAY_TYPES['RETURNED']])],
        ];
    }
}
