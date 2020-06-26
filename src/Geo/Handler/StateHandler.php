<?php

declare(strict_types=1);

namespace Geo\Handler;

use Geo\Entity\CollectionInterface;

use function array_key_exists;

class StateHandler extends MongoRestHandler
{
    use FilterInputQuery;

    public const IDENTIFIER_NAME = 'id';

    public function fetchAll(array $query = [], array $options = []): CollectionInterface
    {
        $queryParams     = $this->request->getQueryParams();
        $sort            = $this->filterString($queryParams['sort'] ?? self::IDENTIFIER_NAME);
        $options['sort'] = [$sort => (int) ($queryParams['order'] ?? -1)];

        if (array_key_exists('name', $queryParams) && ! empty($queryParams['name'])) {
            $query['name'] = $this->filterString($queryParams['name']);
        }

        if (array_key_exists('shortName', $queryParams) && ! empty($queryParams['shortName'])) {
            $query['shortName'] = $this->filterString($queryParams['shortName']);
        }

        return parent::fetchAll($query, $options);
    }
}
