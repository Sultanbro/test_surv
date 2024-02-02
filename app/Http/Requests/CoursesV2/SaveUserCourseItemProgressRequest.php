<?php

namespace App\Http\Requests\CoursesV2;

use Illuminate\Foundation\Http\FormRequest;

class SaveUserCourseItemProgressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'course_item_id' => 'required|int',
            'item_id' => 'required|int',
            'item_type' => 'required|string',
            // all_stages, completed_stages
            'questions' => 'required|array'
            // question_id, answer, user_id, ..courseid...
        ];
    }
}
