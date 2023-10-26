<?php

namespace App\Service\Custom\Files;

use Illuminate\Http\UploadedFile;

trait CanSaveImages
{
    private function saveImage(
        UploadedFile $file,
        string       $path,
    ): string
    {
        return $this->fileManager->apply($file, $path)->url();
    }
}
