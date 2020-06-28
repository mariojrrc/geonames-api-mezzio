<?php

declare(strict_types=1);

namespace Geo\Mapper;

use App\Constants;
use ArrayObject;
use DateTime;
use DateTimeZone;
use Geo\Entity\CollectionInterface;
use Geo\Entity\EntityInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Paginator\Adapter\ArrayAdapter;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

use function array_column;
use function array_map;
use function array_merge;
use function assert;
use function count;
use function date;
use function getenv;

abstract class Mapper implements MapperInterface
{
    protected Collection $mongoCollection;
    private string $entityClass;
    private string $collectionClass;
    private string $inputFilterClass;

    public static int $maxItemPerPage = 100;

    public function __construct(
        Collection $collection,
        string $entityClass,
        string $collectionClass,
        string $inputFilterClass
    ) {
        $this->mongoCollection  = $collection;
        $this->entityClass      = $entityClass;
        $this->collectionClass  = $collectionClass;
        $this->inputFilterClass = $inputFilterClass;
    }

    public function fetchById(string $id): ?EntityInterface
    {
        return $this->fetchOneBy(['_id' => $id]);
    }

    public function fetchOneBy(array $conditions = [], bool $withDeleted = false): ?EntityInterface
    {
        if (! $withDeleted) {
            $conditions['deleted'] = ['$ne' => true];
        }

        $result = $this->mongoCollection->findOne($conditions);
        if ($result === null) {
            return null;
        }

        assert($result instanceof ArrayObject);

        return $this->createFromStorage($result->getArrayCopy());
    }

    protected function createFromStorage(array $data): EntityInterface
    {
        $data['id']        = $data['_id'];
        $timezone          = new DateTimeZone(getenv('TIMEZONE') ?: Constants::TIMEZONE_DEFAULT);
        $data['createdAt'] = $data['createdAt'] instanceof UTCDateTime
            ? $data['createdAt']->toDateTime()->setTimeZone($timezone)->format('c')
            : null;
        $data['updatedAt'] = $data['updatedAt'] instanceof UTCDateTime
            ? $data['updatedAt']->toDateTime()->setTimeZone($timezone)->format('c')
            : null;
        $data['deletedAt'] = isset($data['deletedAt']) && $data['deletedAt'] instanceof UTCDateTime
            ? $data['deletedAt']->toDateTime()->setTimeZone($timezone)->format('c')
            : null;
        $entity            = $this->entityClass::fromArray($data);
        assert($entity instanceof EntityInterface);

        return $entity;
    }

    public function fetchAllBy(
        array $conditions = [],
        bool $withDeleted = false,
        array $options = []
    ): CollectionInterface {
        if (! $withDeleted) {
            $conditions['deleted'] = ['$ne' => true];
        }

        $list   = [];
        $result = $this->mongoCollection->find($conditions, $options);
        if ($result !== null) {
            $list = $result->toArray();
        }

        return $this->createCollectionFromStorage($list);
    }

    protected function createCollectionFromStorage(array $data): CollectionInterface
    {
        $list = [];
        foreach ($data as $entity) {
            $list[] = $this->createFromStorage($entity->getArrayCopy());
        }

        return new $this->collectionClass(new ArrayAdapter($list));
    }

    public function countAllBy(array $conditions = [], bool $withDeleted = false, array $options = []): int
    {
        if (! $withDeleted) {
            $conditions['deleted'] = ['$ne' => true];
        }

        $result = $this->mongoCollection->find($conditions, $options);

        return count($result->toArray());
    }

    public function insert(EntityInterface $entity): EntityInterface
    {
        $data              = $this->extractDataForStorage($entity);
        $now               = new DateTime(date('c'));
        $data['createdAt'] = new UTCDateTime($now);
        $data['updatedAt'] = $data['createdAt'];
        $this->mongoCollection->insertOne($data);

        return $this->createEntity(array_merge($entity->getArrayCopy(), $data));
    }

    protected function extractDataForStorage(EntityInterface $entity): array
    {
        $data        = $entity->getArrayCopy();
        $data['_id'] = $data['id'];
        unset($data['id']);

        return $data;
    }

    public function update(EntityInterface $entity, array $set): EntityInterface
    {
        $set['updatedAt'] = new UTCDateTime(new DateTime(date('c')));
        $this->mongoCollection->updateOne(['_id' => $entity->id()->toString()], ['$set' => $set]);

        return $this->createEntity(array_merge($entity->getArrayCopy(), $set));
    }

    public function delete(EntityInterface $entity): EntityInterface
    {
        $now = new DateTime(date('c'));
        $set = [
            'deleted' => true,
            'deletedAt' => new UTCDateTime($now),
        ];

        return $this->update($entity, $set);
    }

    public function remove(EntityInterface $entity): void
    {
        $this->mongoCollection->deleteOne(['_id' => $entity->id()->toString()]);
    }

    public function createEntity(array $data): EntityInterface
    {
        foreach (['createdAt', 'updatedAt', 'deletedAt'] as $key) {
            if (! isset($data[$key]) || ! ($data[$key] instanceof UTCDateTime)) {
                continue;
            }

            $data[$key] = $data[$key]->toDateTime()
                ->setTimeZone(new DateTimeZone(getenv('TIMEZONE') ?: Constants::TIMEZONE_DEFAULT))
                ->format('c');
        }

        return $this->entityClass::fromArray($data);
    }

    public function createEntityInputFilter(): InputFilterInterface
    {
        return new $this->inputFilterClass();
    }

    public function bulk(array $ids): array
    {
        $result = $this->mongoCollection->find(['_id' => ['$in' => $ids]]);

        return array_column(array_map(
            static function (ArrayObject $item) {
                return [
                    'id' => $item['_id'],
                    'name' => $item['name'],
                    'shortName' => $item['shortName'],
                ];
            },
            $result->toArray()
        ), null, 'id');
    }
}
