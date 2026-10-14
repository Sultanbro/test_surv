<?php

namespace App\Http\Requests\Award;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

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
            'award_category_id' => 'required|integer|exists:award_categories,id',
            'course_ids'        => 'array',
            'styles'            => 'string',
            'targetable_type'   => 'string',
            'targetable_id'     => 'integer',
            'file.*'            => 'file|mimes:jpg,png,pdf|max:7168',
            'preview.*'         => 'file|mimes:jpg,png,pdf|max:7168',
            'type'              => 'string'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'success' => false,
            'error' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }

}
