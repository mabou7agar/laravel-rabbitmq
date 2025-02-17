<?php

declare(strict_types=1);

namespace RabbitMQ\Handlers;

class RabbitMQHandler
{
   public function handle(array $data){
        dump($data);
   }
}
