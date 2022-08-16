<?php

namespace Eddir\Messenger;

use Eddir\Messenger\Console\InstallCommand;
use Eddir\Messenger\Facades\MessengerFacade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use JetBrains\PhpStorm\ArrayShape;

class MessengerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @noinspection PhpCSValidationInspection
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'chat');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        Route::group([
            'namespace' => config('messenger.routes.namespace'),
            'middleware' => config('messenger.routes.middleware'),
            'prefix' => config('messenger.routes.prefix'),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });

        $components = [
            'ChatApp',
            'ChatComponent',
            'ChatConversation',
            'ChatFeed',
            'ChatHeader',
            'ChatsList',
            'MessageInput',
            'SideChatsList',
        ];

        $for_publishing = [];
        foreach ($components as $component) {
            $for_publishing[__DIR__ . "/../resources/js/Components/Chat/$component.vue"] =
                resource_path("js/Components/Chat/$component.vue");
        }

        $this->publishes($for_publishing, 'messenger-views');

        if ($this->app->runningInConsole() or "it's ok to publish views outside of the console" != "") {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('messenger.php'),
            ], 'messenger-config');

            // Publishing the views.
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/eddir/messenger'),
            ], 'messenger-views');

            // Registering package commands.
             $this->commands([InstallCommand::class]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'chat');

        // Register the main class to use with the facade
        $this->app->singleton('messenger', function () {
            return new Messenger();
        });
    }

    /**
     * API routes configurations.
     *
     * @return array
     */
    #[ArrayShape([
    'prefix'     => "\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed",
                    'namespace'  => "\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed",
                    'middleware' => "\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed",
    ])] private function apiRoutesConfigurations(): array
    {
        return [
            'prefix' => config('messenger.api_routes.prefix'),
            'namespace' => config('messenger.api_routes.namespace'),
            'middleware' => config('messenger.api_routes.middleware'),
        ];
    }
}
