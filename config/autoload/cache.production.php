<?php

declare(strict_types=1);

// phpcs:disable
return [
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
];
// phpcs:enable
