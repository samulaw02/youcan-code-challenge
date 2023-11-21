<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

class CustomLoggerChannel
{
    public function __invoke(array $config)
    {
        $logger = new Logger('custom');

        // Determine the environment
        $environment = app()->environment();

        // Choose the appropriate logging handler based on the environment
        $handler = $environment === 'production'
            ? $this->createProductionHandler()
            : $this->createDevelopmentHandler();

        $logger->pushHandler($handler);

        return $logger;
    }

    private function createProductionHandler()
    {
        $handler = new StreamHandler(storage_path('logs/laravel-json.log'), Logger::DEBUG);

        // Use JsonFormatter in production environment
        $handler->setFormatter(new JsonFormatter());

        return $handler;
    }

    private function createDevelopmentHandler()
    {
        return new StreamHandler('php://stdout', Logger::DEBUG);
    }
}
