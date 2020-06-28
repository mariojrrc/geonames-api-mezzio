<?php

declare(strict_types=1);

namespace Geo\Entity;

use App\Constants;
use DateTimeImmutable;
use DateTimeZone;
use Geo\ValueObject\StateId;
use Laminas\InputFilter\InputFilterInterface;
use LosMiddleware\ApiServer\Entity\Entity;
use MongoDB\BSON\Unserializable;
use MongoDB\BSON\UTCDateTime;

use function getenv;

class StateEntity extends Entity implements EntityInterface, Unserializable
{
    private StateId $id;
    private string $name;
    private string $shotName;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private ?bool $deleted;
    private ?DateTimeImmutable $deletedAt;
    // phpcs:disable
    private bool $unserialized = false;
    // phpcs:enable

    private function __construct(
        StateId $id,
        string $name,
        string $shotName,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        ?bool $deleted = null,
        ?DateTimeImmutable $deletedAt = null
    ) {
        $this->id        = $id;
        $this->name      = $name;
        $this->shotName  = $shotName;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deleted   = $deleted;
        $this->deletedAt = $deletedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? StateId::fromString($data['id']) : StateId::generate(),
            $data['name'],
            $data['shortName'],
            new DateTimeImmutable($data['createdAt'] ?? 'now'),
            new DateTimeImmutable($data['updatedAt'] ?? 'now'),
            (bool) ($data['deleted'] ?? false),
            isset($data['deletedAt']) ?  new DateTimeImmutable($data['createdAt']) : null
        );
    }

    public function id(): StateId
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
            'shortName' => $this->shotName,
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
        $this->id           = StateId::fromString($data['_id']);
        $this->name         = $data['name'];
        $this->shotName     = $data['shortName'];
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
