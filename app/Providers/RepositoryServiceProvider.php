<?php

namespace App\Providers;

use App\Repositories\Interfaces\Article\ArticleRepositoryInterface;
use App\Repositories\Interfaces\Article\CommentRepositoryInterface;
use App\Repositories\Interfaces\Article\FileRepositoryInterface;
use App\Repositories\Articles\ArticleRepository;
use App\Repositories\Articles\CommentRepository;
use App\Repositories\Articles\FileRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CommentRepositoryInterface::class,
            CommentRepository::class
        );

        $this->app->bind(
            ArticleRepositoryInterface::class,
            ArticleRepository::class
        );

        $this->app->bind(
            FileRepositoryInterface::class,
            FileRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
