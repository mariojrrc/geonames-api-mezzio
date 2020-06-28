<?php

declare(strict_types=1);

namespace Geo\Mapper;

use Geo\Entity\CollectionInterface;
use Geo\Entity\EntityInterface;
use Laminas\InputFilter\InputFilterInterface;

interface MapperInterface
{
    public function fetchById(string $id): ?EntityInterface;

    public function fetchOneBy(array $conditions = [], bool $withDeleted = false): ?EntityInterface;

    public function fetchAllBy(
        array $conditions = [],
        bool $withDeleted = false,
        array $options = []
    ): CollectionInterface;

    public function countAllBy(array $conditions = [], bool $withDeleted = false, array $options = []): int;

    public function insert(EntityInterface $entity): EntityInterface;

    public function update(EntityInterface $entity, array $set): EntityInterface;

    public function delete(EntityInterface $entity): EntityInterface;

    public function remove(EntityInterface $entity): void;

    public function createEntity(array $data): EntityInterface;

    public function createEntityInputFilter(): InputFilterInterface;

    public function bulk(array $ids): array;
}
