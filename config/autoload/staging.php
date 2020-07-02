<?php

declare(strict_types=1);

/**
 * Staging Heroku configuration.
 */
use Laminas\ConfigAggregator\ConfigAggregator;

// phpcs:disable
return [
    ConfigAggregator::ENABLE_CACHE => false,
    'debug' => getenv('DEBUG'),
    'cache' => [
        'api' => [
            'adapter' => [
                'name' => 'redis',
                'options' => [
                    'server' => getenv('REDIS_URL'),
                    'password' => getenv('REDIS_PASS'),
                ],
            ],
            'plugins' => [
                'serializer',
                'exception_handler' => ['throw_exceptions' => false],
            ],
        ],
        'los_rate_limit' => [
            'adapter' => [
                'name' => 'redis',
                'options' => [
                    'server' => getenv('REDIS_URL'),
                    'password' => getenv('REDIS_PASS'),
                ],
            ],
            'plugins' => [
                'serializer',
                'exception_handler' => ['throw_exceptions' => false],
            ],
        ],
    ],
    // disable losLog local
    'loslog' => [
        'log_dir' => '',
        'error_logger_file' => 'php://stderr',
        'exception_logger_file' => 'php://stderr',
        'static_logger_file' => 'php://stderr',
        'http_logger_file' => 'php://stderr',
        'log_request' => false,
        'log_response' => false,
        'full' => false,
    ],
];
// phpcs:enable
