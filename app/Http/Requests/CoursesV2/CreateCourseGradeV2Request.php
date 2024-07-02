<?php

namespace App\Http\Requests\CoursesV2;

use App\DTO\CoursesV2\CourseGradePropsDto;
use Illuminate\Foundation\Http\FormRequest;

class CreateCourseGradeV2Request extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|int',
            'course_grade' => 'required|int',
            'course_comment' => 'nullable|string',
            'curator_id' => 'nullable|int',
            'curator_grade' => 'nullable|int',
            'curator_comment' => 'nullable|string',
        ];
    }

    /**
     * @return CourseGradePropsDto
     */
    public function toDto(): CourseGradePropsDto
    {
        return new CourseGradePropsDto(
            id: null,
            user_id: null,
            course_id: $this->get('course_id'),
            curator_id: $this->get('curator_id'),
            course_grade: $this->get('course_grade'),
            curator_grade: $this->get('curator_grade'),
            course_comment: $this->get('course_comment'),
            curator_comment: $this->get('curator_comment')
        );
    }
}
