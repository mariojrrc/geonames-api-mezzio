<?php

declare(strict_types=1);

namespace GeoTest\Handler;

use Geo\Handler\StateHandler;
use Geo\Handler\StateHandlerFactory;
use Geo\Mapper\StateMapper;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

class StateHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|ObjectProphecy */
    protected $container;

    protected function setUp() : void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $router = $this->prophesize(RouterInterface::class);

        $mapper = $this->prophesize(StateMapper::class);
        $resource = $this->prophesize(ResourceGenerator::class);
        $hal = $this->prophesize(HalResponseFactory::class);
        $problem = $this->prophesize(ProblemDetailsResponseFactory::class);

        $this->container->get(StateMapper::class)->willReturn($mapper);
        $this->container->get(ResourceGenerator::class)->willReturn($resource);
        $this->container->get(HalResponseFactory::class)->willReturn($hal);
        $this->container->get(ProblemDetailsResponseFactory::class)->willReturn($problem);
        $this->container->get(RouterInterface::class)->willReturn($router);
    }

    public function testFactory()
    {
        $factory = new StateHandlerFactory();
        $this->assertInstanceOf(StateHandlerFactory::class, $factory);

        $homePage = $factory($this->container->reveal());
        $this->assertInstanceOf(StateHandler::class, $homePage);
    }
}
