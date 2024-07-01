<?php

namespace Eddir\Messenger;

use Eddir\Messenger\Console\InstallCommand;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MessengerServiceProvider extends ServiceProvider {
    /**
     * Register the application services.
     */
    public function register() {
        // Automatically apply the package configuration
        $this->mergeConfigFrom( __DIR__ . '/../config/config.php', 'chat' );

        // Register the main class to use with the facade
        $this->app->singleton( 'messenger', function () {
            return new Messenger();
        } );
    }

    /**
     * Bootstrap the application services.
     *
     * @noinspection PhpCSValidationInspection
     */
    public function boot() {
        Route::group( [
            'namespace'  => config( 'messenger.routes.namespace' ),
            'middleware' => config( 'messenger.routes.middleware' ),
            'prefix'     => config( 'messenger.routes.prefix' ),
        ], function () {
            $this->loadRoutesFrom( __DIR__ . '/../routes/web.php' );
        } );

        if ( $this->app->runningInConsole() ) {
            $this->bootForConsole();
        }
    }

    /**
     * Console-specific booting.
     */
    public function bootForConsole() {
            // Registering package commands.
            $this->commands( [ InstallCommand::class ] );

            // Messenger migrations.
            $this->loadMigrationsFrom( __DIR__ . '/../database/migrations/tenant' );

            // Publishing the configuration file.
            $this->publishes( [
                __DIR__ . '/../config/config.php' => config_path( 'messenger.php' ),
            ], 'messenger-config' );

            // Publishing Vue components.
            $this->publishes( [
                __DIR__ . '/../resources/js/components/Chat/' => resource_path( "js/components/Chat/" ),
            ], 'messenger-vue-components' );

            $this->publishes( [
                __DIR__ . '/../public' => public_path( 'vendor/messenger' ),
            ], 'messenger-assets' );
        }
}
