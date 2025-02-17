<?php

declare(strict_types=1);

namespace RabbitMQ\Providers;

use RabbitMQ\Console\ConsumeBroadcast;
use RabbitMQ\Console\SetupBroadcastQueues;
use RabbitMQ\Console\TestQueue;
use BasePackage\Shared\Module\ModuleServiceProvider;
use Illuminate\Support\Facades\Route;

class RabbitMQServiceProvider extends ModuleServiceProvider
{
    public static function getModuleName(): string
    {
        return 'Rabbitmq';
    }

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerMigrations();
        $this->handlePublish();
        $this->registerMigrations();
        $this->mergeRabbitMQConfig();
    }

    public function register(): void
    {
        $this->registerRoutes();
        $this->commands(
            [
                ConsumeBroadcast::class,
                SetupBroadcastQueues::class,
                TestQueue::class
            ]
        );
    }

    public function mapRoutes(): void
    {
        Route::prefix('api/base-rabbitmq')
            ->middleware('api')
            ->group($this->getModulePath() . '/Resources/routes/api.php');
    }

    public function handlePublish()
    {
        $this->publishes(
            [
                __DIR__ . '/../Resources/config/config.php' => config_path('rabbitmq.php')
            ],
            'base-rabbitmq'
        );
    }


    protected function mergeRabbitMQConfig()
    {
        $rabbitmqConfig = config('rabbitmq');
        if ($rabbitmqConfig) {
            unset($rabbitmqConfig['actions']);
            config([
                       'queue.connections.rabbitmq' => array_merge(
                           config('queue.connections.rabbitmq', []),
                           $rabbitmqConfig
                       )
                   ]);
        }
    }
}
