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
        $config = $container->get('config');
        $db     = $config['db']['mongo']['uri'];
        $dbName = $config['db']['mongo']['dbname'];

        $client = new Client($db);

        return new CityMapper(
            $client->selectCollection($dbName, 'cities'),
            CityEntity::class,
            CityCollection::class,
            CityInputFilter::class
        );
    }
}
