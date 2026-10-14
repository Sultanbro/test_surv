<?php

namespace App\Http\Requests;

use App\DTO\RewardDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class RewardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id'   => 'required|numeric|exists:users,id',
            'award_id'  => 'required|numeric|exists:awards,id',
            'course_id' => 'numeric|exists:courses,id',
            'file'      => 'file|mimes:jpg,png,pdf|max:2048',
            'preview'   => 'file|mimes:jpg,png,pdf|max:2048'
        ];
    }

    public function toDto(): RewardDTO
    {
        $transferData = $this->validated();

        $userId = Arr::get($transferData, 'user_id');
        $awardId = Arr::get($transferData, 'award_id');
        $courseId = Arr::get($transferData, 'course_id') ?? null;
        $file = Arr::get($transferData, 'file') ?? null;
        $preview = Arr::get($transferData, 'preview') ?? null;

        return RewardDTO::toArray(
            $userId,
            $courseId,
            $awardId,
            $file,
            $preview
        );
    }
}
