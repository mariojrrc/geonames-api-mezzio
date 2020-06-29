<?php

declare(strict_types=1);

namespace Geo\Mapper;

use Geo\Entity\StateCollection;
use Geo\Entity\StateEntity;
use Geo\Entity\StateInputFilter;
use Interop\Container\ContainerInterface;
use Laminas\Cache\StorageFactory;
use MongoDB\Client;

class StateMapperFactory
{
    public function __invoke(ContainerInterface $container): StateMapper
    {
        $config = $container->get('config');
        $db     = $config['db']['mongo']['uri'];
        $dbName = $config['db']['mongo']['dbname'];

        $client = new Client($db);
        $cache  = StorageFactory::factory($config['cache']['api']);

        return new StateMapper(
            $client->selectCollection($dbName, 'states'),
            StateEntity::class,
            StateCollection::class,
            StateInputFilter::class,
            $cache
        );
    }
}
