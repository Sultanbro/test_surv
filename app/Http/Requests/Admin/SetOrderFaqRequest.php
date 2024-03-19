<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\FaqDTO;
use Illuminate\Foundation\Http\FormRequest;

class SetOrderFaqRequest extends FormRequest
{
    public function prepareForValidation()
    {
//        if ($this->input('items') != null) {
//            dd($this->input('items'));
//            $items = json_decode($this->input('items'), 1);
//        } else {
//            $items = [];
//        }
//dd($items);
//        $this->merge([
//            'items' => $items,
//        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'items' => ['required','array'],
            'items.*.id' => ['required'],
            'items.*.parent_id' => ['nullable', 'int'],
            'items.*.order' => ['required', 'int'],
        ];
    }
}
