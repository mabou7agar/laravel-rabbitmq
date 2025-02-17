<?php

declare(strict_types=1);

namespace RabbitMQ\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeBroadcast extends Command
{
    protected $signature = 'rabbitmq:consume-broadcast {queue}';

    public function handle()
    {
        $connectionConfig = Config::get('rabbitmq.connection');

        $connection = new AMQPStreamConnection(
            $connectionConfig['host'],
            $connectionConfig['port'],
            $connectionConfig['user'],
            $connectionConfig['password'],
            $connectionConfig['vhost']
        );

        $channel = $connection->channel();
        $queueName = $this->argument('queue');

        $callback = function (AMQPMessage $msg) {
            echo " [x] Queue {$this->argument('queue')} received: ", $msg->body, "\n";
            $msg->ack();
        };

        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        $this->info(" [*] Waiting for messages on queue: $queueName");

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
