<?php

declare(strict_types=1);

namespace RabbitMQ\Controllers;

use App\Http\Controllers\Controller;
use RabbitMQ\Jobs\BroadcastMessage;

class RabbitMQController extends Controller
{
    public function sendMessage()
    {
        BroadcastMessage::broadcastToExchange('user_created',['test'=>'test']);

        return response()->json(['status' => 'Message published successfully']);
    }
}
