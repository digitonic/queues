<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class QueueInstallerTest extends TestCase
{
    /** @test */
    public function can_install_package()
    {
        File::delete(config_path('digitonic/queues.php'));
        File::delete(base_path('tools/docker/etc/confd/templates/supervisor/workers.tmpl'));

        $this->artisan('digitonic:queues:install');

        $this->assertTrue(File::exists(config_path('digitonic/queues.php')));
        $this->assertTrue(File::exists(base_path('tools/docker/etc/confd/templates/supervisor/workers.tmpl')));

        File::delete(config_path('digitonic/queues.php'));
        File::delete(base_path('tools/docker/etc/confd/templates/supervisor/workers.tmpl'));
    }
}
