<?php

namespace Digitonic\Queues;

use Digitonic\Queues\Commands\QueuesInstaller;
use Digitonic\Queues\Commands\QueuesPublisher;
use Illuminate\Support\ServiceProvider;

class QueuesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('digitonic/queues.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../stubs/workers.tmpl' => base_path('tools/docker/etc/confd/templates/supervisor/workers.tmpl'),
                __DIR__.'/../stubs/slack-notifier.tmpl' => base_path('tools/docker/etc/confd/templates/supervisor/slack-notifier.tmpl'),
                __DIR__.'/../stubs/supervisor_workers.toml' => base_path('tools/docker/etc/confd/conf.d/supervisor_workers.toml'),
                __DIR__.'/../stubs/supervisor_slack_notifier.toml' => base_path('tools/docker/etc/confd/conf.d/supervisor_slack_notifier.toml'),
                __DIR__.'/../stubs/notifier.py' => base_path('notifier.py'),
            ]);

            // Registering package commands.
            $this->commands([
                QueuesInstaller::class,
                QueuesPublisher::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'digitonic/queues');
    }
}
