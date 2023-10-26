<?php

namespace App\Helpers;

use App\Downloads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileHelper
{

    public static function save(UploadedFile $file, string $path): ?string
    {
//        $storage = Storage::disk('public');
        $storage = Storage::disk('s3');
//        try {
        $result = false;

        if ($file->isValid()) {
            $path = self::checkDirectory($path);
            $result = $storage->putFile($path, $file);
            $result = $result ? basename($result) : null;
        }

        return $result;
//
//        } catch (Throwable) {
//            return null;
//        }
    }

    public static function delete(string $filename, string $path): bool
    {
        $storage = Storage::disk('s3');

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
        $storage = Storage::disk('s3');


        if (!$storage->directoryExists($path)) {
            $storage->makeDirectory($path);
        }

        return $path;
    }

    public static function checkFile(string $path): string
    {
        $storage = Storage::disk('s3');

        return $storage->exists($path);
    }

    public static function getPath(string $folder, string $filename): string
    {
        $path = self::checkDirectory($folder);
        return Storage::disk('s3')->path($path . '/' . $filename);
    }

    public static function getUrl(string $folder, string|null $filename): string
    {
        return Storage::disk('s3')
            ->temporaryUrl(($folder !== '' ? ($folder . '/') : '') . $filename, now()->addMinutes(360));
    }

    /**
     * @param array $files
     * @param int $userId
     * @return void
     */
    public static function storeDocumentsFile(
        array $files,
        int   $userId
    ): void
    {
        $downloads = [
            'user_id' => $userId,
            'ud_lich' => null,
            'dog_okaz_usl' => null,
            'sohr_kom_tainy' => null,
            'dog_o_nekonk' => null,
            'trud_dog' => null,
            'archive' => null,
        ];

        if ($files['dog_okaz_usl']) {
            $file = $files['dog_okaz_usl'];
            $name = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = '/dog_okaz_usl';
            $downloads['dog_okaz_usl'] = $name;
            $file->move("static/profiles/" . $userId . $path, $name);
        }
        if ($files['sohr_kom_tainy']) {
            $file = $files['sohr_kom_tainy'];
            $name = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = '/sohr_kom_tainy';
            $downloads['sohr_kom_tainy'] = $name;
            $file->move("static/profiles/" . $userId . $path, $name);
        }
        if ($files['dog_o_nekonk']) {
            $file = $files['dog_o_nekonk'];
            $name = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = '/dog_o_nekonk';
            $downloads['dog_o_nekonk'] = $name;
            $file->move("static/profiles/" . $userId . $path, $name);
        }
        if ($files['trud_dog']) {
            $file = $files['trud_dog'];
            $name = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = '/trud_dog';
            $downloads['trud_dog'] = $name;
            $file->move("static/profiles/" . $userId . $path, $name);
        }
        if ($files['ud_lich']) {
            $file = $files['ud_lich'];
            $name = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = '/ud_lich';
            $downloads['ud_lich'] = $name;
            $file->move("static/profiles/" . $userId . $path, $name);
        }
        if ($files['photo']) {
            $file = $files['photo'];
            $name = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = '/photo';
            $file->move("static/profiles/" . $userId . $path, $name);
        }
        if ($files['archive']) {
            $file = $files['archive'];
            $name = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = '/archive';
            $downloads['archive'] = $name;
            $file->move("static/profiles/" . $userId . $path, $name);
        }

        $existedDownloads = Downloads::query()
            ->where('user_id', $userId)
            ->first();

        if ($existedDownloads) {
            foreach ($downloads as $key => $item) {
                if ($item) {
                    $existedDownloads[$key] = $item;
                }
            }
            $existedDownloads->save();
        } else {
            Downloads::query()->create(
                [
                    'user_id' => $userId
                ],
                $downloads
            );
        }

    }
}
