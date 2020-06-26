<?php

declare(strict_types=1);

namespace Geo\Handler;

use Laminas\Filter;

trait FilterInputQuery
{
    private function filterString($value): ?string
    {
        $finalValue = (new Filter\StringTrim())->filter($value);
        $finalValue = (new Filter\StripTags())->filter($finalValue);
        $finalValue = (new Filter\StripNewlines())->filter($finalValue);

        return ! empty($finalValue) ? $finalValue : null;
    }
}
