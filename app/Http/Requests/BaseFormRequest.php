<?php

namespace App\Http\Requests;

use App\DTO\BaseDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseFormRequest extends FormRequest
{
    abstract public function rules(): array;

    abstract public function authorize(): bool;

    abstract public function toDto(): BaseDTO;

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message'   => $validator->errors()->first(),
            'data'      => []
        ]));
    }
}