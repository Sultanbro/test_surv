<?php

namespace App\Http\Requests\Files;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class FileStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file'],
            'name' => ['nullable', 'string']
        ];
    }

    public function getFile(): UploadedFile
    {
        return $this->file('file');
    }
}
