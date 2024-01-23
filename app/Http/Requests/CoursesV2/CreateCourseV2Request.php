<?php

namespace App\Http\Requests\CoursesV2;

use App\Models\CourseV2;
use App\Models\CourseItemV2;
use Illuminate\Validation\Rule;
use App\DTO\CoursesV2\CoursePropsDto;
use Illuminate\Foundation\Http\FormRequest;

class CreateCourseV2Request extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Step 1
            'name' => 'required|string',
            'short' => 'required|string',
            'desc' => 'required|string',
            'icon' => 'required',
            'background' => 'required',

            // Step 2
            'elements.*' => [
                'item_type' => [
                    'required',
                    Rule::in(CourseItemV2::ITEM_TYPES)
                ],
                'item_id' => 'required',
                'file' => 'required_if:item-type,4', // 4 -> iSpring
                'order' => ''
            ],

            // Step 3
            'type' => [
                'required',
                Rule::in([CourseV2::AUTOMATIC_TYPE, CourseV2::INDIVIDUAL_TYPE])
            ],
            'targets.*' => [
                'target_type' => [
                    'required', Rule::in(['App\\User', 'App\\ProfileGroup', 'App\\Position', 'All'])
                ],
                'target_id' => 'required',
            ],

            // Step 4
            'passing_score' => 'required|int|max:100',
            'attempts' => 'required|int',
            'mix_questions' => 'required|int|in:0,1',
            'show_answers' => 'required|int|in:0,1',
            'start' => 'required_if:type,2', // 2 -> Individual
            'stop' => 'required_if:type,2', // 2 -> Individual

            // Step 5
            'curator_id' => 'required',
            'curator_group_id' => '',
            'curator_position_id' => '',
            'notifications',
            'award_id' => 'required|int',
            'show_as_finished' => 'required|int|in:0,1',
            'bonus' => 'required|int',

            // Step 7
            'for_sale' => 'required|int|in:0,1',
            'cat_id' => 'required|int',
            'author' => 'required|string',
            'slides' => 'required|array',
            'slides.*' => 'required|string',
        ];
    }

    /**
     * @return CoursePropsDto
     */
    public function toDto(): CoursePropsDto
    {
        return new CoursePropsDto(
            id: null,
            name: $this->get('name'),
            short: $this->get('short'),
            desc: $this->get('desc'),
            icon: $this->file('icon'),
            background: $this->file('background'),
            elements: $this->get('elements'),
            type: $this->get('type'),
            targets: $this->get('targets'),
            passing_score: $this->get('passing_score'),
            attempts: $this->get('attempts'),
            mix_questions: $this->get('mix_questions'),
            show_answers: $this->get('show_answers'),
            start: $this->get('start'),
            stop: $this->get('stop'),
            curator_id: $this->get('curator_id'),
            curator_group_id: $this->get('curator_group_id'),
            curator_position_id: $this->get('curator_position_id'),
            notifications: $this->get('notifications'),
            award_id: $this->get('award_id'),
            show_as_finished: $this->get('show_as_finished'),
            bonus: $this->get('bonus'),
            for_sale: $this->get('for_sale'),
            cat_id: $this->get('cat_id'),
            author: $this->get('author'),
            slides: $this->get('slides')
        );
    }
}
