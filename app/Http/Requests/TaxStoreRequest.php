<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxStoreRequest extends FormRequest
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
            'name'      => 'required|string',
            'amount'    => 'required|numeric',
            'percent'   => 'required|numeric|max:100',
            'user_id'   => 'integer'
        ];
    }


    public function messages()
    {
        return [
            'name.required'     => 'Поле имя обязательное.',
            'name.string'       => 'Поле имя должна быть строкой.',
            'amount.required'   => 'Поле сумма обязательна.',
            'amount.numeric'    => 'Поле сумма должна быть цифрой.',
            'percent.required'  => 'Поле процент обязательно',
            'percent.numeric'   => 'Поле процент должна быть цифрой',
            'percent.max'       => 'Максимальное значение для поле процент 100'
        ];
    }
}
