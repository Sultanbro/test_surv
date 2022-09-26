<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KpiSaveRequest extends FormRequest
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
            'targetable_id'         => 'integer|required',
            'targetable_type'       => 'required',
            'completed_80'          => 'required',
            'completed_100'         => 'required',
            'lower_limit'           => 'required',
            'upper_limit'           => 'required',
            'colors'                => 'array|nullable',
            'items.*.name'          => 'required',
            'items.*.plan'          => 'required|numeric',
            'items.*.share'          => 'required|numeric',
            'items.*.activity_id'   => 'required|integer'
        ];
    }
}
