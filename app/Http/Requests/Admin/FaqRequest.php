<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\FaqDTO;
use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'int|nullable',
            'title' => 'required|string',
            'page' => 'required|string',
            'body' => 'required|string',
            'order' => 'required|int',
        ];
    }

    /**
     * @return FaqDTO
     */
    public function toDto(): FaqDTO
    {
        return new FaqDTO(
            parent_id: $this->get('parent_id'),
            title: $this->get('title'),
            page: $this->get('page'),
            body: $this->get('body'),
            order: $this->get('order'),
        );
    }
}
