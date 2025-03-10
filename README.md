## Installation

### Composer Require
``` bash
composer require m-tech-stack/rabbit-mq
```
### Run Vendor
``` bash
php artisan vendor:publish --tag="base-rabbitmq" --force
```

### How To Use

#### Test Broadcast Message
``` bash
php artisan rabbitmq:setup-broadcast
```
``` bash
php artisan rabbitmq:test-broadcast 
```
``` bash
php artisan queue:work --queue=user_notification_queue
```

#### Add Following To YOur Env File
``` bash
RABBITMQ_HOST=
RABBITMQ_PORT=
RABBITMQ_USER=
RABBITMQ_PASSWORD=
RABBITMQ_VHOST=
```
#### Use Action Array At RabbitMQ config 
``` bash
'actions' => [
        [
        'action' => 'test', // Action Name
        'class' => RabbitMQ\Handlers\RabbitMQHandler::class //Action Handler
        'queue' => 'default' // Specify Queue if needed
        ]  
```
