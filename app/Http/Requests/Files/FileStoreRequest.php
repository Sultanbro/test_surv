<?php

namespace App\Http\Requests\Files;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FileStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => [Rule::requiredIf(Str::lower($this->method()) !== 'put'), 'file'],
            'local_name' => ['nullable', 'string']
        ];
    }

    public function getFile(): UploadedFile
    {
        return $this->file('file');
    }
}
