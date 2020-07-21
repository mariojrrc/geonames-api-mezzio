<?php

declare(strict_types=1);

namespace GeoTest\Entity;

use DateTimeImmutable;
use Geo\Entity\CityEntity;
use Geo\Entity\CityInputFilter;
use Geo\Entity\EntityInterface;
use Geo\ValueObject\CityId;
use Geo\ValueObject\StateId;
use Laminas\InputFilter\InputFilterAwareInterface;
use MongoDB\BSON\Unserializable;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;

class CityEntityTest extends TestCase
{
    public function testConstruct()
    {
        $data = [
            'id' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'name' => ' Rio de Janeiro ',
            'stateId' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'createdAt' => '2020-05-08 12:26:19-03:00',
            'updatedAt' => '2020-05-08 12:26:19-03:00',
        ];
        $entity = CityEntity::fromArray($data);
        $this->assertInstanceOf(CityId::class, $entity->id());
        $this->assertSame($data['name'], $entity->name());
        $this->assertInstanceOf(StateId::class, $entity->stateId());
        $this->assertSame($data['stateId'], $entity->stateId()->toString());
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->createdAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->updatedAt());
        $this->assertSame(false, $entity->deleted());
        $this->assertNull($entity->deletedAt());

        $this->assertInstanceOf(InputFilterAwareInterface::class, $entity);
        $this->assertInstanceOf(EntityInterface::class, $entity);
        $this->assertInstanceOf(Unserializable::class, $entity);
    }

    public function testGetArrayCopy()
    {
        $data = [
            'id' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'name' => ' Rio de Janeiro ',
            'stateId' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'createdAt' => '2020-05-08 12:26:19',
            'updatedAt' => '2020-05-08 12:26:19',
        ];
        $entity = CityEntity::fromArray($data)->getArrayCopy('Y-m-d H:i:s');
        $this->assertSame($data['id'], $entity['id']);
        $this->assertSame($data['name'], $entity['name']);
        $this->assertSame($data['stateId'], $entity['stateId']);
        $this->assertSame($data['createdAt'], $entity['createdAt']);
        $this->assertSame($data['updatedAt'], $entity['updatedAt']);
        $this->assertSame(false, $entity['deleted']);
        $this->assertNull($entity['deletedAt']);

        $data['deleted'] = true;
        $data['deletedAt'] = '2020-05-08 12:26:19';
        $entityDeleted = CityEntity::fromArray($data)->getArrayCopy('Y-m-d H:i:s');
        $this->assertSame(true, $entityDeleted['deleted']);
        $this->assertSame($data['deletedAt'], $entityDeleted['deletedAt']);
    }

    public function testGetInputFilter()
    {
        $data = [
            'id' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'name' => ' Rio de Janeiro ',
            'stateId' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'createdAt' => '2020-05-08 12:26:19',
            'updatedAt' => '2020-05-08 12:26:19',
        ];
        $entity = CityEntity::fromArray($data);
        $this->assertInstanceOf(CityInputFilter::class, $entity->getInputFilter());

        $inputFilter = $entity->getInputFilter();

        // empty data
        $inputFilter->setData([])->isValid();
        $this->assertCount(2, $inputFilter->getMessages());
        $this->assertArrayHasKey('name', $inputFilter->getMessages());
        $this->assertArrayHasKey('stateId', $inputFilter->getMessages());

        // invalid data
        $inputFilter->setData([
            'name' => 'Mussum Ipsum, cacilds vidis litro abertis. Todo mundo vê os porris que eu tomo, mas ninguém vê os
             tombis que eu levo! Viva Forevis aptent taciti sociosqu ad litora torquent. Si u mundo tá muito paradis?
             Toma um mé que o mundo vai girarzis! Em pé sem cair, deitado sem dormir, sentado sem cochilar e fazendo pose.
             Praesent vel viverra nisi. Mauris aliquet nunc non turpis scelerisque, eget. Nec orci ornare consequat.',
            'stateId' => 'xxx'
        ])->isValid();
        $this->assertCount(2, $inputFilter->getMessages());
        $this->assertArrayHasKey('name', $inputFilter->getMessages());
        $this->assertArrayHasKey('stringLengthTooLong', $inputFilter->getMessages()['name']);
        $this->assertArrayHasKey('stateId', $inputFilter->getMessages());
        $this->assertArrayHasKey('valueNotUuid', $inputFilter->getMessages()['stateId']);
    }

    public function testBsonUnserialize()
    {
        $data = [
            '_id' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'name' => ' Rio de Janeiro ',
            'stateId' => '87b4da28-d4b4-484a-846d-acbe4d9bdbf3',
            'createdAt' => new UTCDateTime(new DateTimeImmutable('2020-05-08 12:26:19')),
            'updatedAt' => new UTCDateTime(new DateTimeImmutable('2020-05-08 12:26:19')),
        ];
        $bson = fromJSON(json_encode($data));
        $entity = toPHP($bson, ['root' => CityEntity::class]);

        $this->assertInstanceOf(CityId::class, $entity->id());
        $this->assertSame($data['name'], $entity->name());
        $this->assertInstanceOf(StateId::class, $entity->stateId());
        $this->assertSame($data['stateId'], $entity->stateId()->toString());
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->createdAt());
        $this->assertSame('2020-05-08 12:26:19', $entity->createdAt()->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->updatedAt());
        $this->assertSame('2020-05-08 12:26:19', $entity->updatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame(false, $entity->deleted());
        $this->assertNull($entity->deletedAt());

        $data['deleted'] = true;
        $data['deletedAt'] = new UTCDateTime(new DateTimeImmutable('2020-05-08 12:26:20'));
        $bson = fromJSON(json_encode($data));
        $entity = toPHP($bson, ['root' => CityEntity::class]);
        $this->assertSame(true, $entity->deleted());
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->deletedAt());
        $this->assertSame('2020-05-08 12:26:20', $entity->deletedAt()->format('Y-m-d H:i:s'));
    }
}
