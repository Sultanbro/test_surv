<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileHelper
{

    public static function save(UploadedFile $file, string $path): ?string
    {
        $storage =  Storage::disk('s3');

        try {
            $result = null;

            if ($file->isValid()) {
                $path = self::checkDirectory($path);
                $result = $storage->putFile($path, $file);
                $result = $result ? basename($result) : null;
            }

            return $result;

        } catch (Throwable $ex) {
            return null;
        }
    }

    public static function delete(string $filename, string $path): bool
    {
        $storage =  Storage::disk('s3');

        try {
            $path = self::checkDirectory($path);

            $storage->delete($path . '/' . $filename);

            return true;

        } catch (Throwable $ex) {
            return false;
        }
    }

    private static function checkDirectory(string $path): string
    {
        $storage =  Storage::disk('s3');


        if (!$storage->directoryExists($path)) {
            $storage->makeDirectory($path);
        }

        return $path;
    }

    public static function checkFile(string $path): string
    {
        $storage =  Storage::disk('s3');

        return $storage->exists($path);
    }

    public static function getPath(string $folder, string $filename): string
    {
        $path = self::checkDirectory($folder);
        return Storage::disk('s3')->path($path . '/' . $filename);
    }

    public static function getUrl(string $folder, string $filename): string
    {
        return Storage::disk('s3')
            ->temporaryUrl(
                ($folder !== '' ? ($folder . '/') : '') . $filename,
                now()->addMinutes(360));
    }
}
