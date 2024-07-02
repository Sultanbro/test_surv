<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatedUserStatUpdateRequest extends FormRequest
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
            'user_id'       => ['required', 'numeric', 'exists:users,id'],
            'activity_id'   => ['required', 'numeric'],
            'date'          => ['required', 'string'],
            'kpi_item_id'   => ['required', 'numeric', 'exists:kpi_items,id'],
            'value'         => ['required', 'numeric']
        ];
    }
}
