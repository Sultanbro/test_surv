<?php

namespace App\Service\Uploads;

use App\Helpers\FileHelper;
use Illuminate\Http\UploadedFile;

class UploadService
{
    /**
     * @param UploadedFile $file
     * @return string
     */
    public function store(UploadedFile $file): string
    {
        $filename = FileHelper::save($file, config('app.upload.path'));
        return FileHelper::getUrl(config('app.upload.path'), $filename);
    }
}
