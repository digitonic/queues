<?php

namespace Digitonic\Queues\Tests;

use Orchestra\Testbench\TestCase;
use Digitonic\Queues\QueuesServiceProvider;

class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            QueuesServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }
}
