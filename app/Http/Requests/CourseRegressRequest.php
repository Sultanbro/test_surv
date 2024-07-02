<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRegressRequest extends FormRequest
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
            'user_id'           => ['required', 'exists:users,id'],
            'course_item_id'    => ['required_if:type,item', 'exists:course_items,id'],
            'course_id'         => ['required_if:type,course', 'exists:courses,id'],
            'type'              => ['required', Rule::in(['item', 'course'])],
        ];
    }
}
