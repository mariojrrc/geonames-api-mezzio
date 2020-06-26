<?php

declare(strict_types=1);

namespace Geo\Entity;

use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class StateInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'required' => false,
            'validators' => [
                ['name' => Validator\Uuid::class],
            ],
            'filters' => [],
            'name' => 'id',
        ]);

        $this->add([
            'required' => true,
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 255,
                        'inclusive' => true,
                    ],
                ],
            ],
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
                ['name' => Filter\StripNewlines::class],
            ],
            'name' => 'name',
        ]);

        $this->add([
            'required' => true,
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 3,
                        'inclusive' => true,
                    ],
                ],
            ],
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
                ['name' => Filter\StripNewlines::class],
            ],
            'name' => 'shortName',
        ]);
    }
}
