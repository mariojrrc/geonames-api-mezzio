<?php

declare(strict_types=1);

namespace Geo\Handler;

use Geo\Mapper\CityMapper;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Container\ContainerInterface;

class CityHandlerFactory
{
    public function __invoke(ContainerInterface $container): CityHandler
    {
        return new CityHandler(
            $container->get(CityMapper::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
