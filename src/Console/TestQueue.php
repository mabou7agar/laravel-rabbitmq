<?php

declare(strict_types=1);

namespace RabbitMQ\Console;

use RabbitMQ\Jobs\BroadcastMessage;
use Illuminate\Console\Command;

class TestQueue extends Command
{
    protected $signature = 'rabbitmq:test-broadcast';

    public function handle()
    {
        BroadcastMessage::broadcastToExchange('test',['test'=>'this is new queue test']);
    }
}
