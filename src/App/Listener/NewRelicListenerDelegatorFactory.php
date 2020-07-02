<?php

declare(strict_types=1);

namespace App\Listener;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Laminas\Stratigility\Middleware\ErrorHandler;

use function assert;

class NewRelicListenerDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(
        ContainerInterface $container,
        $name,
        callable $callback,
        ?array $options = null
    ) {
        $errorHandler = $callback();
        assert($errorHandler instanceof ErrorHandler);
        $errorHandler->attachListener(new NewRelicListener());

        return $errorHandler;
    }
}
