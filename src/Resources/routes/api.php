<?php


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use RabbitMQ\Controllers\RabbitMQController;

if (!App::environment('production')) {
    Route::get('/', [RabbitMQController::class, 'sendMessage']);
}
