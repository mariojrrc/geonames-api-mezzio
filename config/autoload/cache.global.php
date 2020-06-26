<?php

declare(strict_types=1);

// phpcs:disable
return [
    'cache' => [
        'api' => [
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'namespace' => '',
                    'cache_dir' => 'data/cache/api',
                    'dir_level' => 1,
                    'dir_permission' => 0777,
                    'file_permission' => 0666,
                    'ttl' => 300,
                ],
            ],
            'plugins' => [
                'serializer',
                'exception_handler' => ['throw_exceptions' => false],
            ],
        ],
        'los_rate_limit' => [
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'namespace' => '',
                    'cache_dir' => 'data/cache/rate_limit',
                    'dir_level' => 1,
                    'dir_permission' => 0777,
                    'file_permission' => 0666,
                    'ttl' => 300,
                ],
            ],
            'plugins' => [
                'serializer',
                'exception_handler' => ['throw_exceptions' => false],
            ],
        ],
    ],
];
