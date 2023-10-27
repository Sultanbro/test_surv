<?php

namespace App\Service\Custom\S3;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class S3Manager
{
    private FilesystemAdapter|Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('s3');
    }

    public function storeFile($filename, $contents): bool|string
    {
        return $this->disk->put($filename, $contents);
    }

    public function retrieveFile($filename): ?string
    {
        return $this->disk->get($filename);
    }

    public function fileExists($filename): bool
    {
        return $this->disk->exists($filename);
    }

    public function deleteFile($filename): bool
    {
        return $this->disk->delete($filename);
    }

    public function generateTemporaryUrl($filename, $duration): string
    {
        return $this->disk->temporaryUrl($filename, now()->addMinutes($duration));
    }
}