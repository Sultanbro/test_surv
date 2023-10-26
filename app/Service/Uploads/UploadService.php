<?php

namespace App\Service\Uploads;

use App\Service\Custom\Files\FileManager;
use Illuminate\Http\UploadedFile;

class UploadService
{
    /**
     * @param UploadedFile $file
     * @return string
     */
    public function store(UploadedFile $file): string
    {
//        $filename = FileHelper::save($file, "uploads");
//        return FileHelper::getUrl('uploads', $filename);
        /** @var FileManager $manager */
        $manager = app(FileManager::class);
        $manager->apply($file, 'uploads');
        return $manager->url();
    }
}
