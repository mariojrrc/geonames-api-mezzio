<?php

declare(strict_types=1);

namespace Geo\Entity;

use Geo\ValueObject\CityId;
use Geo\ValueObject\StateId;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\Stdlib\ArraySerializableInterface;

interface EntityInterface extends ArraySerializableInterface, InputFilterAwareInterface
{
    /**
     * @return CityId|StateId
     */
    public function id();
}
