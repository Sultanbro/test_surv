<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KpiUpdateRequest extends FormRequest
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
            'completed_80'          => 'nullable',
            'completed_100'         => 'nullable',
            'lower_limit'           => 'nullable',
            'upper_limit'           => 'nullable',
            'colors'                => 'array|nullable',
            // 'items.*.name'          => 'nullable|string',
            // 'items.*.plan'          => 'nullable|numeric',
            // 'items.*.share'          => 'nullable|numeric',
            // 'items.*.activity_id'   => 'nullable|numeric',
            // 'items.*.deleted'       => 'nullable|bool'
        ];
    }
}
