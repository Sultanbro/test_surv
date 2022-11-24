<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileHelper
{
    const PUBLIC_FOLDER = 'public';

    public static function save(UploadedFile $file, string $path): ?string
    {
        try {
            $result = null;

            if ($file->isValid()) {
                $path = self::checkDirectory($path);
                $result = Storage::disk()->putFile($path, $file);
                $result = $result ? basename($result) : null;
            }

            return $result;

        } catch (Throwable $ex) {
            return null;
        }
    }

    public static function delete(string $filename, string $path): bool
    {
        try {
            $path = self::checkDirectory($path);

            Storage::delete($path . '/' . $filename);

            return true;

        } catch (Throwable $ex) {
            return false;
        }
    }

    private static function checkDirectory(string $path): string
    {
        $path = self::PUBLIC_FOLDER . '/' . $path;

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        return $path;
    }

    public static function getPath(string $folder, string $filename): string
    {
        $path = self::checkDirectory($folder);
        return Storage::disk()->path($path . '/' . $filename);
    }

    public static function getUrl(string $folder, string $filename): string
    {
        return Storage::disk()->url(($folder !== '' ? ($folder . '/') : '') . $filename);
    }
}
