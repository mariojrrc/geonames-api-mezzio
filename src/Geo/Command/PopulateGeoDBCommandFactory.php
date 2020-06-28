<?php

declare(strict_types=1);

namespace Geo\Command;

use Interop\Container\ContainerInterface;
use MongoDB\Client;

class PopulateGeoDBCommandFactory
{
    public function __invoke(ContainerInterface $container): PopulateGeoDBCommand
    {
        $db     = $container->get('config')['db']['mongo']['uri'];
        $dbName = $container->get('config')['db']['mongo']['dbname'];
        $client = new Client($db);

        return new PopulateGeoDBCommand($client, $dbName);
    }
}
