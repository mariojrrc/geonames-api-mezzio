<?php

declare(strict_types=1);

namespace Geo;

use Geo\Entity\CityCollection;
use Geo\Entity\CityEntity;
use Geo\Entity\StateCollection;
use Geo\Entity\StateEntity;
use Geo\Handler\CityHandler;
use Geo\Handler\CityHandlerFactory;
use Geo\Handler\StateBulkHandler;
use Geo\Handler\StateBulkHandlerFactory;
use Geo\Handler\StateHandler;
use Geo\Handler\StateHandlerFactory;
use Geo\Mapper\CityMapper;
use Geo\Mapper\CityMapperFactory;
use Geo\Mapper\StateMapper;
use Geo\Mapper\StateMapperFactory;
use Laminas\Hydrator\ArraySerializableHydrator;
use Mezzio\Hal\Metadata\MetadataMap;
use Mezzio\Hal\Metadata\RouteBasedCollectionMetadata;
use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;

/**
 * The configuration provider for the Geo module
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
            MetadataMap::class => $this->getMetadataMaps(),
            'commands' => $this->getCommands(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories'  => [
                StateHandler::class => StateHandlerFactory::class,
                CityHandler::class => CityHandlerFactory::class,
                StateBulkHandler::class => StateBulkHandlerFactory::class,

                StateMapper::class => StateMapperFactory::class,
                CityMapper::class => CityMapperFactory::class,

                Command\PopulateGeoDBCommand::class => Command\PopulateGeoDBCommandFactory::class,
            ],
        ];
    }

    public function getMetadataMaps(): array
    {
        return [
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => StateEntity::class,
                'route' => 'state.entity',
                'extractor' => ArraySerializableHydrator::class,
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => StateCollection::class,
                'collection_relation' => 'states',
                'route' => 'state.collection',
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => CityEntity::class,
                'route' => 'city.entity',
                'extractor' => ArraySerializableHydrator::class,
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => CityCollection::class,
                'collection_relation' => 'cities',
                'route' => 'city.collection',
            ],
        ];
    }

    private function getCommands(): array
    {
        return [
            Command\PopulateGeoDBCommand::class,
        ];
    }
}
