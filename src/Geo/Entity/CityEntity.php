<?php

declare(strict_types=1);

namespace Geo\Entity;

use App\Constants;
use DateTimeImmutable;
use DateTimeZone;
use Geo\ValueObject\CityId;
use Geo\ValueObject\StateId;
use Laminas\InputFilter\InputFilterInterface;
use LosMiddleware\ApiServer\Entity\Entity;
use MongoDB\BSON\Unserializable;
use MongoDB\BSON\UTCDateTime;

use function getenv;

class CityEntity extends Entity implements EntityInterface, Unserializable
{
    private CityId $id;
    private string $name;
    private StateId $stateId;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private ?bool $deleted;
    private ?DateTimeImmutable $deletedAt;
    // phpcs:disable
    private bool $unserialized = false;
    // phpcs:enable

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

    public function name(): string
    {
        return $this->name;
    }

    public function stateId(): StateId
    {
        return $this->stateId;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deleted(): ?bool
    {
        return $this->deleted;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function getInputFilter(): InputFilterInterface
    {
        if ($this->inputFilter === null) {
            $this->inputFilter = new CityInputFilter();
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

    public function bsonUnserialize(array $data): void
    {
        $this->id           = CityId::fromString($data['_id']);
        $this->name         = $data['name'];
        $this->stateId      = StateId::fromString($data['stateId']);
        $timezone           = new DateTimeZone(getenv('TIMEZONE') ?: Constants::TIMEZONE_DEFAULT);
        $this->createdAt    = new DateTimeImmutable($data['createdAt'] instanceof UTCDateTime
            ? $data['createdAt']->toDateTime()->setTimezone($timezone)->format('c') : 'now');
        $this->updatedAt    = new DateTimeImmutable($data['updatedAt'] instanceof UTCDateTime
            ? $data['updatedAt']->toDateTime()->setTimezone($timezone)->format('c') : 'now');
        $this->deletedAt    = ($data['deletedAt'] ?? '') instanceof UTCDateTime
            ? new DateTimeImmutable($data['deletedAt']->toDateTime()->setTimezone($timezone)->format('c'))
            : null;
        $this->deleted      = (bool) ($data['deleted'] ?? false);
        $this->unserialized = true;
    }
}
