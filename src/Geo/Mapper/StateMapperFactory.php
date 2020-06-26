<?php

declare(strict_types=1);

namespace Geo\Mapper;

use Geo\Entity\StateCollection;
use Geo\Entity\StateEntity;
use Geo\Entity\StateInputFilter;
use Interop\Container\ContainerInterface;
use MongoDB\Client;

class StateMapperFactory
{
    public function __invoke(ContainerInterface $container): StateMapper
    {
        $db     = $container->get('config')['db']['mongo']['uri'];
        $client = new Client($db);

        return new StateMapper(
            $client->selectCollection('geonames', 'states'),
            StateEntity::class,
            StateCollection::class,
            StateInputFilter::class
        );
    }
}
