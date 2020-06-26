<?php

declare(strict_types=1);

use Geo\Handler\CityHandler;
use Geo\Handler\StateHandler;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/health', App\Handler\HealthHandler::class, 'health');
    $app->route(
        '/v1/state',
        StateHandler::class,
        ['GET', 'POST'],
        'state.collection'
    );
    $app->route(
        '/v1/state/{id}',
        StateHandler::class,
        ['GET', 'PATCH', 'DELETE', 'PUT'],
        'state.entity'
    );
    $app->route(
        '/v1/city',
        CityHandler::class,
        ['GET', 'POST'],
        'city.collection'
    );
    $app->route(
        '/v1/city/{id}',
        CityHandler::class,
        ['GET', 'PATCH', 'DELETE', 'PUT'],
        'city.entity'
    );
};
