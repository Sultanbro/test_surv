<?php

namespace App\Http\Requests\CoursesV2;

use App\Models\CourseV2;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\DTO\CoursesV2\AssignedCourseFilterPropsDto;

class FilterAssignedCourseV2Request extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'target',
            'type' => [Rule::in([CourseV2::AUTOMATIC_TYPE, CourseV2::INDIVIDUAL_TYPE, CourseV2::PURCHASED_TYPE])],
            'curator_id' => 'int',
            'created_date' => 'string',
            'stop_date' => 'string',
            'per_page' => 'int'
        ];
    }

    /**
     * @return AssignedCourseFilterPropsDto
     */
    public function toDto(): AssignedCourseFilterPropsDto
    {
        return new AssignedCourseFilterPropsDto(
            search: $this->get('search'),
            target: $this->get('target'),
            type: $this->get('type'),
            curator_id: $this->get('curator_id'),
            created_date: $this->get('created_date'),
            stop_date: $this->get('stop_date'),
            per_page: $this->get('per_page')
        );
    }
}
