<?php

namespace Digitonic\Queues\Commands;

use Digitonic\Queues\QueuesServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class QueuesInstaller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'digitonic:queues:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install digitonic queues system';

    public function handle()
    {
        Artisan::call('vendor:publish', ['--provider' => QueueServiceProvider::class]);
    }
}
