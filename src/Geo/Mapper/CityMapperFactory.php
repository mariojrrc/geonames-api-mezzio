<?php

declare(strict_types=1);

namespace Geo\Mapper;

use Geo\Entity\CityCollection;
use Geo\Entity\CityEntity;
use Geo\Entity\CityInputFilter;
use Interop\Container\ContainerInterface;
use MongoDB\Client;

class CityMapperFactory
{
    public function __invoke(ContainerInterface $container): CityMapper
    {
        $db     = $container->get('config')['db']['mongo']['uri'];
        $client = new Client($db);

        return new CityMapper(
            $client->selectCollection('geonames', 'cities'),
            CityEntity::class,
            CityCollection::class,
            CityInputFilter::class
        );
    }
}
