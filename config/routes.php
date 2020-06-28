<?php

declare(strict_types=1);

use Geo\Handler\CityHandler;
use Geo\Handler\StateBulkHandler;
use Geo\Handler\StateHandler;
use LosMiddleware\RateLimit\RateLimitMiddleware;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/health', App\Handler\HealthHandler::class, 'health');
    $app->route(
        '/v1/state/bulk',
        [RateLimitMiddleware::class, StateBulkHandler::class],
        ['POST', 'OPTIONS'],
        'state.bulk'
    );
    $app->route(
        '/v1/state',
        [RateLimitMiddleware::class, StateHandler::class],
        ['GET', 'POST', 'OPTIONS'],
        'state.collection'
    );
    $app->route(
        '/v1/state/{id}',
        [RateLimitMiddleware::class, StateHandler::class],
        ['GET', 'PATCH', 'DELETE', 'PUT', 'OPTIONS'],
        'state.entity'
    );
    $app->route(
        '/v1/city',
        [RateLimitMiddleware::class, CityHandler::class],
        ['GET', 'POST', 'OPTIONS'],
        'city.collection'
    );
    $app->route(
        '/v1/city/{id}',
        [RateLimitMiddleware::class, CityHandler::class],
        ['GET', 'PATCH', 'DELETE', 'PUT', 'OPTIONS'],
        'city.entity'
    );
};
