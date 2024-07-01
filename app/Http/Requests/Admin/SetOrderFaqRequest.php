<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\FaqDTO;
use Illuminate\Foundation\Http\FormRequest;

class SetOrderFaqRequest extends FormRequest
{
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
