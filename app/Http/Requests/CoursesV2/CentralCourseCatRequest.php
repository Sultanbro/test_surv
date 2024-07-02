<?php

namespace App\Http\Requests\CoursesV2;

use App\Models\CourseV2;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\DTO\CoursesV2\AssignedCourseFilterPropsDto;

class CentralCourseCatRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'order' => 'required|int'
        ];
    }
}
