<?php

namespace App\Http\Requests\Uploads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file',],
        ];
    }

    public function getFile(): UploadedFile
    {
        return $this->file('file');
    }
}
