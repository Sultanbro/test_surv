<?php

namespace App\Service\Uploads;

use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use Illuminate\Http\UploadedFile;

class UploadService
{
    /**
     * @param UploadedFile $file
     * @return string
     * @throws BusinessLogicException
     */
    public function store(UploadedFile $file): string
    {
        if (!$filename = FileHelper::save($file, config('app.upload.path'))) {
            throw new BusinessLogicException(__('exception.upload_error'));
        }

        return FileHelper::getUrl(config('app.upload.path'), $filename);
    }
}
