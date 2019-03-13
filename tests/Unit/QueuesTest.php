<?php

namespace Digitonic\Queues\Tests\Unit\Queues;

use Digitonic\Queues\Queue;
use Tests\TestCase;

class QueuesTest extends TestCase
{
    /** @test */
    public function facade_returns_queue_name()
    {
        config(['digitonic.queues.test' => 'test-queue']);

        $this->assertEquals('test-queue', Queue::test());
    }
}
