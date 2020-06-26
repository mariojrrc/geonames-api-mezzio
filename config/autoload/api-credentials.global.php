<?php

declare(strict_types=1);

// todo generate this credentials from database
return [
    'los' => [
        'api_server' => [
            'auth' => [
                'api-keys' => [
                    getenv('ADMIN_API_KEY') => [ // Admin
                        'allowed-routes' => ['*'],
                        'rate-limit' => [
                            'max_requests' => 500, // 1000 / seconds
                            'reset_time' => 1,
                        ],
                    ],
                    getenv('CLIENTS_API_KEY') => [ // Clients
                        'allowed-routes' => [
                            'state.entity' => ['GET', 'HEAD', 'OPTIONS'],
                            'state.collection' => ['GET', 'HEAD', 'OPTIONS'],
                            'city.entity' => ['GET', 'HEAD', 'OPTIONS'],
                            'city.collection' => ['GET', 'HEAD', 'OPTIONS'],
                        ],
                        'rate-limit' => [
                            'max_requests' => 100, // 50 / seconds
                            'reset_time' => 1,
                        ],
                    ],
                ],
            ],
            'open-routes' => [ 'health' ],
        ],
    ],
];
