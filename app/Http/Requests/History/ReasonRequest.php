<?php

namespace App\Http\Requests\History;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ReasonRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string']
        ];
    }

    public function action(): string
    {
        $data = [
            'post' => 'Восстановлено',
            'delete' => 'Удалено',
        ];

        return $data[Str::lower($this->method())];
    }

    public function reason(): string
    {
        return $this->validated('reason');
    }
}
