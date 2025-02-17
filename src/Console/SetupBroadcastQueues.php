<?php

declare(strict_types=1);

namespace RabbitMQ\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SetupBroadcastQueues extends Command
{
    protected $signature = 'rabbitmq:setup-broadcast';

    public function handle()
    {
        $connectionConfig = Config::get('rabbitmq.connection');
        $exchanges = Config::get('rabbitmq.exchanges');
        $queues = Config::get('rabbitmq.queues');

        $connection = new AMQPStreamConnection(
            $connectionConfig['host'],
            $connectionConfig['port'],
            $connectionConfig['user'],
            $connectionConfig['password'],
            $connectionConfig['vhost']
        );

        $channel = $connection->channel();

        // Declare exchanges
        foreach ($exchanges as $exchange) {
            $channel->exchange_declare(
                $exchange['name'],
                $exchange['type'],
                $exchange['passive'],
                $exchange['durable'],
                $exchange['auto_delete']
            );
        }

        // Declare queues
        foreach ($queues as $queue) {
            // Get associated exchange
            $exchangeConfig = $exchanges[$queue['exchange']];
            // Declare queue
            $channel->queue_declare(
                $queue['name'],
                $queue['passive'],
                $queue['durable'],
                $queue['exclusive'],
                $queue['auto_delete']
            );
            $channel->queue_bind(
                $queue['name'],
                $exchangeConfig['name']
            );

            $this->info("Created and bound queue: ".$queue['name']);

            // Enable publisher confirms
            $channel->confirm_select();
        }
            $channel->close();
            $connection->close();
        }
    }
