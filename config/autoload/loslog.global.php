<?php

declare(strict_types=1);

use Laminas\Stratigility\Middleware\ErrorHandler;

return [
    'dependencies' => [
        'factories' => [
            Psr\Log\LoggerInterface::class => LosMiddleware\LosLog\LoggerFactory::class,
        ],
        'delegators' => [
            ErrorHandler::class => [
                LosMiddleware\LosLog\ErrorHandlerListenerDelegatorFactory::class,
            ],
        ],
    ],
    'loslog' => [
        'log_dir' => 'data/logs',
        'error_logger_file' => 'error.log',
        'exception_logger_file' => 'exception.log',
        'static_logger_file' => 'static.log',
        'http_logger_file' => 'http.log',
        'log_request' => false,
        'log_response' => false,
        'full' => false,
    ],
];
