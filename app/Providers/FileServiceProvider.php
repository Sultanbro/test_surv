<?php

namespace App\Providers;

use App\Service\Custom\Files\FileManager;
use App\Service\Custom\Files\FileManagerInterface;
use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FileManagerInterface::class, FileManager::class);
    }
}
