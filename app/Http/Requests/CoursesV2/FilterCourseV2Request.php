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
            'targets' => 'array',
            'targets.*' => [
                'target_type' => ['required', Rule::in(CourseV2::TARGET_TYPES)],
                'target_id' => 'required',
            ],
            'type' => [Rule::in([CourseV2::AUTOMATIC_TYPE, CourseV2::INDIVIDUAL_TYPE])],
            'for_sale' => 'nullable|in:0,1',
            'created_date' => 'date',
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
            targets: $this->get('targets'),
            type: $this->get('type'),
            for_sale: $this->get('for_sale'),
            created_date: $this->get('created_date'),
            per_page: $this->get('per_page')
        );
    }
}
