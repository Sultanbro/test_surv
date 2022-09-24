<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreAwardRequest extends FormRequest
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
            'award_type_id' => 'required|integer',
            'format'        => Rule::in(['jpg', 'png', 'pdf']),
            'image'          => File::types(['jpg', 'png', 'pdf'])->min(0)->max(2048)
        ];
    }
}
