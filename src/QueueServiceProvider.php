<?php

namespace Digitonic\Queues;

use Digitonic\Queues\Commands\QueuesInstaller;
use Digitonic\Queues\Commands\QueuesPublisher;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/queues.php', 'digitonic.queues');

        $this->commands([
            QueuesInstaller::class,
            QueuesPublisher::class,
        ]);

        $this->publishes([
            __DIR__.'/../config/queues.php' => config_path('digitonic/queues.php'),
            __DIR__.'/../stubs/workers.tmpl' => base_path('tools/docker/etc/confd/templates/supervisor/workers.tmpl'),
            __DIR__.'/../stubs/slack-notifier.tmpl' => base_path('tools/docker/etc/confd/templates/supervisor/slack-notifier.tmpl'),
            __DIR__.'/../stubs/supervisor_workers.toml' => base_path('tools/docker/etc/confd/conf.d/supervisor_workers.toml'),
            __DIR__.'/../stubs/supervisor_slack_notifier.toml' => base_path('tools/docker/etc/confd/conf.d/supervisor_slack_notifier.toml'),
            __DIR__.'/../stubs/notifier.py' => base_path('notifier.py'),
        ]);
    }
}
