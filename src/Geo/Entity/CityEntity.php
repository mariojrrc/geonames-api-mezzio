<?php

declare(strict_types=1);

namespace Geo\Entity;

use DateTimeImmutable;
use Geo\ValueObject\CityId;
use Geo\ValueObject\StateId;
use Laminas\InputFilter\InputFilterInterface;
use LosMiddleware\ApiServer\Entity\Entity;

class CityEntity extends Entity implements EntityInterface
{
    private CityId $id;
    private string $name;
    private StateId $stateId;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private ?bool $deleted;
    private ?DateTimeImmutable $deletedAt;

    private function __construct(
        CityId $id,
        string $name,
        StateId $stateId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        ?bool $deleted = null,
        ?DateTimeImmutable $deletedAt = null
    ) {
        $this->id        = $id;
        $this->name      = $name;
        $this->stateId   = $stateId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deleted   = $deleted;
        $this->deletedAt = $deletedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? CityId::fromString($data['id']) : CityId::generate(),
            $data['name'],
            StateId::fromString($data['stateId']),
            new DateTimeImmutable($data['createdAt'] ?? 'now'),
            new DateTimeImmutable($data['updatedAt'] ?? 'now'),
            (bool) ($data['deleted'] ?? false),
            isset($data['deletedAt']) ?  new DateTimeImmutable($data['createdAt']) : null
        );
    }

    public function id(): CityId
    {
        return $this->id;
    }

    public function getInputFilter(): InputFilterInterface
    {
        if ($this->inputFilter === null) {
            $this->inputFilter = new StateInputFilter();
        }

        return $this->inputFilter;
    }

    public function getArrayCopy(string $dateFormat = 'c'): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'stateId' => $this->stateId->toString(),
            'createdAt' => $this->createdAt->format($dateFormat),
            'updatedAt' => $this->updatedAt->format($dateFormat),
            'deleted' => $this->deleted,
            'deletedAt' => $this->deletedAt instanceof DateTimeImmutable
                ? $this->deletedAt->format($dateFormat)
                : null,
        ];
    }
}
