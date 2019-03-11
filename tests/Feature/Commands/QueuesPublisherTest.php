<?php

namespace Tests\Feature\Queues\Commands;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class QueuesPublisherTest extends TestCase
{
    /** @test */
    public function can_publish_queues()
    {
        config([
            'digitonic.queues.queueNameForAccessor' => 'test',
            'digitonic.queues.anotherQueueNameForAccessor' => 'test-2'
        ]);

        File::delete(base_path('tools/docker/etc/confd/templates/env/dotenv.tmpl'));
        File::delete(base_path('tools/docker/usr/local/share/env/999-queues-env'));
        File::delete(base_path('workers-production.yml'));
        File::delete(base_path('workers-staging.yml'));

        $this->artisan('digitonic:queues:publish');

        $this->assertTrue(File::exists(base_path('workers-production.yml')));
        $this->assertTrue(File::exists(base_path('workers-staging.yml')));

        $this->assertTrue(File::exists(base_path('tools/docker/etc/confd/templates/env/dotenv.tmpl')));
        $this->assertEquals(
            "QUEUE_NAME_QUEUE_NAME_FOR_ACCESSOR=\"{{ getenv \"QUEUE_NAME_QUEUE_NAME_FOR_ACCESSOR\" }}\";\nQUEUE_NAME_ANOTHER_QUEUE_NAME_FOR_ACCESSOR=\"{{ getenv \"QUEUE_NAME_ANOTHER_QUEUE_NAME_FOR_ACCESSOR\" }}\";\n",
            File::get(base_path('tools/docker/etc/confd/templates/env/dotenv.tmpl'))
        );

        $this->assertTrue(File::exists(base_path('tools/docker/usr/local/share/env/999-queues-env')));
        $this->assertEquals(
            "export QUEUE_NAME_QUEUE_NAME_FOR_ACCESSOR=\${QUEUE_NAME_QUEUE_NAME_FOR_ACCESSOR:-test}\nexport QUEUE_NAME_ANOTHER_QUEUE_NAME_FOR_ACCESSOR=\${QUEUE_NAME_ANOTHER_QUEUE_NAME_FOR_ACCESSOR:-test-2}\n",
            File::get(base_path('tools/docker/usr/local/share/env/999-queues-env'))
        );

        File::delete(base_path('tools/docker/etc/confd/templates/env/dotenv.tmpl'));
        File::delete(base_path('tools/docker/usr/local/share/env/999-queues-env'));
        File::delete(base_path('workers-production.yml'));
        File::delete(base_path('workers-staging.yml'));
    }
}
