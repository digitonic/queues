<?php

namespace Digitonic\Queues\Tests\Unit\Queues;

use Digitonic\Queues\Queue;
use Digitonic\Queues\Tests\BaseTestCase;

class QueuesTest extends BaseTestCase
{
    /** @test */
    public function facade_returns_queue_name()
    {
        $this->app['config']->set('digitonic.queues.test', 'test-queue');

        $this->assertEquals('test-queue', Queue::test());
    }
}
