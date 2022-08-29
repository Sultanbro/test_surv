<?php

namespace Eddir\Messenger\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    protected $signature = 'messenger:install';

    protected $description = 'Install MessengerFacade';

    public function handle()
    {
        $this->updateNodePackages(function ($packages) {
            return [
                       "vue-draggable-resizable" => "^2.3.0",
                       "moment"                  => "",
                       "vue-simple-context-menu" => "3.4.2",
                        "vue-multiselect"        => "^2.1.6",
                   ] + $packages;
        });

        Artisan::call('migrate', [
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'messenger-config',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'messenger-vue-components',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'messenger-assets',
            '--force' => true,
        ]);

        $this->info('MessengerFacade installed successfully.');
    }

    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[ $configurationKey ] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[ $configurationKey ] : [],
            $configurationKey
        );

        ksort($packages[ $configurationKey ]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }
}
