<?php

declare(strict_types=1);

namespace Geo\Paginator;

use Laminas\Paginator\Adapter\AdapterInterface;
use MongoDB\Collection;
use MongoDB\Driver\Query;

class MongoMapperAdapter implements AdapterInterface
{
    private Collection $collection;
    private array $conditions;
    private array $options;
    private string $entityClass;

    public function __construct(
        Collection $collection,
        array $conditions = [],
        array $options = [],
        string $entityClass = ''
    ) {
        $this->collection  = $collection;
        $this->conditions  = $conditions;
        $this->options     = $options;
        $this->entityClass = $entityClass;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Laminas\Paginator\Adapter\AdapterInterface::getItems()
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->options['skip']  = $offset;
        $this->options['limit'] = $itemCountPerPage;

        $query  = new Query($this->conditions, $this->options);
        $cursor = $this->collection->getManager()->executeQuery($this->collection->getNamespace(), $query);
        $cursor->setTypeMap(['root' => $this->entityClass]);

        return $cursor->toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @see Countable::count()
     */
    public function count()
    {
        return $this->collection->countDocuments($this->conditions, $this->options);
    }
}
