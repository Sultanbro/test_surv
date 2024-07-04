<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\PaperDTO;
use Illuminate\Foundation\Http\FormRequest;

class PaperRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'body' => 'required|string',
            'image' => 'required|file',
            'publish' => 'int',
        ];
    }

    /**
     * @return PaperDTO
     */
    public function toDto(): PaperDTO
    {
        return new PaperDTO(
            title: $this->get('title'),
            description: $this->get('description'),
            body: $this->get('body'),
            image: $this->file('image'),
            publish: $this->get('publish')
        );
    }
}
