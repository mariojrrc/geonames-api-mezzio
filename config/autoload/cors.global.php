<?php

declare(strict_types=1);

use App\Middleware\CorsMiddlewareFactory;
use Tuupola\Middleware\CorsMiddleware;

return [
    'cors' => [
        'origin' => ['*'],
        'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        'headers.allow' => ['Content-Type', 'Accept'],
        'headers.expose' => [],
        'credentials' => false,
        'cache' => 0,
    ],
    'dependencies' => [
        'factories' => [
            CorsMiddleware::class => CorsMiddlewareFactory::class,
        ],
    ],
];
