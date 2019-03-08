<?php

namespace Digitonic\Queues\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class QueuesPublisher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'digitonic:queues:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish digitonic queues in environment';

    public function handle()
    {
        $queuesDotEnv = "\n";
        $queuesEnv = '';

        if (File::exists(base_path('docker-compose.yml'))) {
            $dockerCompose = Yaml::parse(File::get(base_path('docker-compose.yml')));
        }

        if (File::exists(base_path('continuous-pipe.yml'))) {
            $continuousPipe = Yaml::parse(File::get(base_path('continuous-pipe.yml')));

            $stagingStub = Yaml::parse(File::get(__DIR__ . '/../../stubs/worker_staging.yml'));
            $productionStub = Yaml::parse(File::get(__DIR__ . '/../../stubs/worker_production.yml'));
            $defaultQueueName = strtolower(config('app.name')) . '-default';

            $stagingStub['workers_staging']['deploy']['services']['worker-default']['specification']['environment_variables']['QUEUE_NAME']
                = $defaultQueueName;
            $stagingStub['workers_staging']['deploy']['services']['worker-default']['specification']['environment_variables'] = array_merge(
                $stagingStub['workers_staging']['deploy']['services']['worker-default']['specification']['environment_variables'],
                $this->getWebEnvVariables($continuousPipe)
            );

            $productionStub['workers_production']['deploy']['services']['worker-default']['specification']['environment_variables']['QUEUE_NAME']
                = $defaultQueueName;
            $productionStub['workers_production']['deploy']['services']['worker-default']['specification']['environment_variables'] = array_merge(
                $productionStub['workers_production']['deploy']['services']['worker-default']['specification']['environment_variables'],
                $this->getWebEnvVariables($continuousPipe)
            );

            foreach ($continuousPipe['pipelines'] as $key => $value) {
                $continuousPipe['pipelines'][$key]['tasks'][]
                    = $continuousPipe['pipelines'][$key]['name'] == 'Production' ? 'workers_production' : 'workers_staging';
            }

            $continuousPipe['tasks']['workers_staging'] = $stagingStub['workers_staging'];
        }

        if (!File::isDirectory(base_path('tools/docker/etc/confd/templates/env/'))) {
            File::makeDirectory(base_path('tools/docker/etc/confd/templates/env/'), 0775, true);
        }

        if (!File::isDirectory(base_path('tools/docker/usr/local/share/env/'))) {
            File::makeDirectory(base_path('tools/docker/usr/local/share/env/'), 0775, true);
        }

        if (!File::exists(base_path('tools/docker/etc/confd/templates/env/dotenv.tmpl'))) {
            File::put(base_path('tools/docker/etc/confd/templates/env/dotenv.tmpl'), '');
            $queuesDotEnv = '';
        }

        if (!File::exists(base_path('tools/docker/usr/local/share/env/999-queues-env'))) {
            File::put(base_path('tools/docker/usr/local/share/env/999-queues-env'), '');
            $queuesEnv = '';
        }


        foreach (config('digitonic.queues') as $queueName => $queueValue) {
            $envName = strtoupper(snake_case($queueName));
            $queuesEnv .= 'export ' . $envName . '=${' . $envName . ':-' . $queueValue . '}';
            $queuesEnv .= "\n";

            $queuesDotEnv .= $envName . '="{{ getenv "' . $envName . '" }}";';
            $queuesDotEnv .= "\n";

            if (isset($dockerCompose)) {
                $dockerCompose['services']['web']['environment'][$envName] = '${' . $envName . '}';
            }

            if (isset($continuousPipe)) {
                $productionStub['workers_production']['deploy']['services'] = array_merge(
                    $productionStub['workers_production']['deploy']['services'],
                    $this->getWorkerYamlConfig($queueName, $queueValue, $this->getWebEnvVariables($continuousPipe))
                );
            }
        }

        File::append(base_path('tools/docker/etc/confd/templates/env/dotenv.tmpl'), $queuesDotEnv);

        File::append(base_path('tools/docker/usr/local/share/env/999-queues-env'), $queuesEnv);

        if (isset($dockerCompose)) {
            File::put(base_path('docker-compose.yml'), Yaml::dump($dockerCompose, 10, 2));
        }

        if (isset($continuousPipe)) {
            File::put(base_path('workers-staging.yml'), Yaml::dump($stagingStub, 10, 2));
            File::put(base_path('workers-production.yml'), Yaml::dump($productionStub, 10, 2));
            $this->info('Workers .yml files were created at the root of your project');
            $this->info('Please replace the placeholders, and add them to your continuous-pipe file in [tasks] and add the tasks to your pipelines');
        }
    }

    /**
     * @param $continuousPipe
     * @return array
     */
    protected function getWebEnvVariables($continuousPipe)
    {
        $webEnvVariables = [];
        collect($continuousPipe['tasks']['initialization']['run']['environment_variables'])->each(function ($item) use (&$webEnvVariables) {
            $webEnvVariables[$item['name']] = $item['value'];
        });
        return $webEnvVariables;
    }

    protected function getWorkerYamlConfig($queueName, $queueValue, $webEnvVariables)
    {
        return [
            str_replace('queue-name', 'worker', kebab_case($queueName)) => [
                'specification' => [
                    'accessibility' => ['from_external' => false],
                    'scalability' => ['number_of_replicas' => '${WORKER_DEFAULT_REPLICAS}'],
                    'source' => ['from_service' => 'web'],
                    'ports' => [80],
                    'environment_variables' => array_merge(
                        [
                            'START_MODE' => 'cron',
                            'RUN_LARAVEL_CRON' => false,
                            'START_QUEUE' => true,
                            'QUEUE_NUM_PROCS' => '1',
                            'QUEUE_NAME' => $queueValue
                        ],
                        $webEnvVariables
                    ),
                    'resources' => [
                        'requests' => [
                            'cpu' => '<placeholder>',
                            'memory' => '<placeholder>',
                        ]
                    ]
                ]
            ]
        ];
    }
}
