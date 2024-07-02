<?php

namespace App\Service\Tools;

use Exception;
use Illuminate\Support\Facades\Log;

class Debugger
{
    public static function error(Exception $e, string $fileName = null, string $driver = 'single'): void
    {
        $fileName = $fileName ?: 'error';

        Log::build([
            'driver' => $driver,
            'path' => storage_path("logs/$fileName.log"),
        ])->debug([
            'line' => $e->getLine(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'message' => $e->getMessage(),
        ]);
    }

    /**
     * Logging The data
     *
     * @param string $fileName
     * @param string|array $log
     * @param string $driver
     */
    public static function debug(string $fileName, string|array $log, string $driver = 'single'): void
    {
        Log::build([
            'driver' => $driver,
            'path' => storage_path('logs/' . $fileName . '.log'),
        ])->debug(json_encode($log));
    }
}
