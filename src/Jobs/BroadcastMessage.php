<?php

declare(strict_types=1);

namespace RabbitMQ\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use PhpAmqpLib\Message\AMQPMessage;

class BroadcastMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;
    protected $action;

    public function __construct(string $action, array $payload)
    {
        $this->payload = $payload;
        $this->action = $action;
    }

    public function handle()
    {
        $actions = Config::get('rabbitmq.actions') ?? [];

        foreach ($actions as $action){
            if ($this->action == $action['action']) {
                $class = app()->make($action['class']);
                $class->handle($this->payload);
            }
        }
    }

    public static function broadcastToExchange($action, $payload, $exchange = 'user_events_exchange')
    {
        $queue = Queue::connection('rabbitmq');

        // Format the job payload like Laravel does
        $payload = [
            'displayName' => static::class,
            'job' => 'Illuminate\Queue\CallQueuedHandler@call',
            'maxTries' => 5,
            'delay' => null,
            'timeout' => null,
            'timeoutAt' => null,
            'data' => [
                'commandName' => static::class,
                'command' => serialize(new static($action, $payload))
            ]
        ];

        // Create AMQP Message with the properly formatted payload
        $message = new AMQPMessage(json_encode($payload), [
            'content_type' => 'application/json',
            'delivery_mode' => 2 // make message persistent
        ]);

        // Publish to exchange
        $queue->getChannel()->basic_publish(
            $message,
            $exchange,
            ''
        );
    }
}
