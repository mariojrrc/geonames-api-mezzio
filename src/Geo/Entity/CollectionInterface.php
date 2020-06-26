<?php

declare(strict_types=1);

namespace Geo\Entity;

use Countable;
use IteratorAggregate;

interface CollectionInterface extends Countable, IteratorAggregate
{
}
