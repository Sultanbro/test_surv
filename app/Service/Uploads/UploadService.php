<?php

namespace App\Service\Uploads;

use App\Service\Custom\S3\S3Manager;
use Illuminate\Http\UploadedFile;

class UploadService
{
    /**
     * @param UploadedFile $file
     * @return string
     */
    public function store(UploadedFile $file): string
    {
        /** @var S3Manager $s3Manager */
        $s3Manager = app(S3Manager::class);
        $contents = file_get_contents($file->getRealPath());
        $filename = $file->getClientOriginalName();
        $s3Manager->storeFile($filename, $contents);
        return $s3Manager->generateTemporaryUrl($filename, 60 * 6);
    }
}
