<?php

namespace App\Http\Requests\Files;

use App\Models\File\File;
use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function getFile(): File
    {
        return File::findOrFail($this->route('file_id'));
    }
}
