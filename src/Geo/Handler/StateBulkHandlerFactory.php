<?php

declare(strict_types=1);

namespace Geo\Handler;

use Geo\Mapper\StateMapper;
use Psr\Container\ContainerInterface;

class StateBulkHandlerFactory
{
    public function __invoke(ContainerInterface $container): StateBulkHandler
    {
        return new StateBulkHandler($container->get(StateMapper::class));
    }
}
