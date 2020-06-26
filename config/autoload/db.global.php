<?php

declare(strict_types=1);

return [
    'db' => [
        'mongo' => [
            'uri' => getenv('MONGODB_URI'),
        ],
    ],
];
