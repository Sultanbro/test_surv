<?php

namespace App\Http\Requests;

use App\DTO\CourseProgressDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ProgressCourseRequest extends FormRequest
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
            'userId' => ['required', 'numeric', 'exists:users,id'],
            'courseId' => ['required', 'numeric', 'exists:courses,id'],
        ];
    }

    /**
     * @return CourseProgressDTO
     */
    public function toDto(): CourseProgressDTO
    {
        $transferData = $this->validated();

        $userId     = Arr::get($transferData, 'userId') ?? null;
        $courseId   = Arr::get($transferData, 'courseId') ?? null;

        return CourseProgressDTO::toArray($userId, $courseId);
    }
}
