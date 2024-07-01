<?php

namespace App\Http\Requests\Courses;

use App\DTO\BaseDTO;
use App\DTO\Courses\GetCourseItemResult;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetCourseItemResultRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id'    => 'required|array',
            'id.*'  => 'integer|exists:courses,id'
        ];
    }

    /**
     * @return BaseDTO
     */
    public function toDto(): BaseDTO
    {
        $validated = $this->validated();

        $ids = Arr::get($validated, 'id');

        return new GetCourseItemResult($ids);
    }
}
