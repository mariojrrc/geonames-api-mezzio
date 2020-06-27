<?php
require __DIR__ . '/../vendor/autoload.php';

define('URL_API', 'http://localhost:8080');
define('REQUEST_ID', \Ramsey\Uuid\Uuid::uuid4()->toString());
