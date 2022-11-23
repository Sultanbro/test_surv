<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'user_id'  => 'required|numeric|exists:users,id',
            'award_id' => 'required|numeric|exists:awards,id',
            'file'          => 'file|mimes:jpg,png,pdf|max:2048'
        ];
    }
}
