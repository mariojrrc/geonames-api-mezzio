<?php

declare(strict_types=1);

namespace App;

use App\Middleware\AuthMiddleware;
use App\Middleware\AuthMiddlewareFactory;
use App\Middleware\SetupTranslatorMiddleware;
use App\Middleware\SetupTranslatorMiddlewareFactory;
use App\Middleware\VersionMiddleware;
use App\Middleware\VersionMiddlewareFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\HealthHandler::class => Handler\HealthHandler::class,
            ],
            'factories'  => [
                AuthMiddleware::class => AuthMiddlewareFactory::class,
                VersionMiddleware::class => VersionMiddlewareFactory::class,
                SetupTranslatorMiddleware::class => SetupTranslatorMiddlewareFactory::class,
            ],
        ];
    }
}
