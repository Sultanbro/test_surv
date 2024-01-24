<?php

namespace App\Http\Requests\CoursesV2;

use App\Models\CourseV2;
use Illuminate\Validation\Rule;
use App\DTO\CoursesV2\CourseFilterPropsDto;
use Illuminate\Foundation\Http\FormRequest;

class FilterCourseV2Request extends FormRequest
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
            'profile_group_id' => 'nullable|int',
            'position_id' => 'nullable|int',
            'type' => [Rule::in([CourseV2::AUTOMATIC_TYPE, CourseV2::INDIVIDUAL_TYPE])],
            'for_sale' => 'nullable|in:0,1',
            'created_date' => 'string',
            'per_page' => 'int'
        ];
    }

    /**
     * @return CourseFilterPropsDto
     */
    public function toDto(): CourseFilterPropsDto
    {
        return new CourseFilterPropsDto(
            search: $this->get('search'),
            profile_group_id: $this->get('profile_group_id'),
            position_id: $this->get('position_id'),
            type: $this->get('type'),
            for_sale: $this->get('for_sale'),
            created_date: $this->get('created_date'),
            per_page: $this->get('per_page')
        );
    }
}
