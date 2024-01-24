<?php

namespace App\Http\Requests\CoursesV2;

use App\Models\CourseV2;
use App\Models\CourseItemV2;
use Illuminate\Validation\Rule;
use App\DTO\CoursesV2\CoursePropsDto;
use Illuminate\Foundation\Http\FormRequest;

class CreateCourseV2Request extends FormRequest
{
    public function prepareForValidation()
    {
        $elements = [];
        $targets = [];
        $notifications = [];
        $slides = [];

        if ($this->input('elements') != null) $elements = json_decode($this->input('elements'), true);

        if ($this->input('targets') != null) $targets = json_decode($this->input('targets'), true);

        if ($this->input('slides') != null) $slides = json_decode($this->input('slides'));

        $this->merge([
            'elements' => $elements,
            'targets' => $targets,
            'slides' => $slides
        ]);
    }

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
            'icon' => 'required|file',
            'background' => 'required|file',

            // Step 2
            'elements' => 'required|array|min:1',
            'elements.*.item_type' => ['required', Rule::in(CourseItemV2::ITEM_TYPES)],
            'elements.*.item_id' => 'required|integer',
            'elements.*.file' => 'required_with:item_id', // item id will not be while uploading iSpring
            'elements.*.name' => 'required',
            'elements.*.order' => 'required',
            'elements.*.duration' => 'required', // minutes


            // Step 3
            'type' => ['required', Rule::in([CourseV2::AUTOMATIC_TYPE, CourseV2::INDIVIDUAL_TYPE])],
            'targets' => 'array',
            'targets.*.target_type' => ['required', Rule::in(CourseV2::TARGET_TYPES)],
            'targets.*.target_id' => 'required|int',

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
            'notifications' => 'array',
            'award_id' => 'required|int',
            'show_as_finished' => 'required|int|in:0,1',
            'bonus' => 'required|int',

            // Step 7
            'for_sale' => 'required|int|in:0,1',
            'cat_id' => 'required_if:for_sale,1|int',
            'price' => 'required_if:for_sale,1|int',
            'author' => 'required_if:for_sale,1|string',
            'slides' => 'required_if:for_sale,1|array',
            'slides.*' => 'required_if:for_sale,1|string',
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
            price: $this->get('price'),
            cat_id: $this->get('cat_id'),
            author: $this->get('author'),
            slides: $this->get('slides')
        );
    }
}
